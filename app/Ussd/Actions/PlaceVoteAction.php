<?php

namespace App\Ussd\Actions;

use App\Models\EligibleVoter;
use App\Models\Vote;
use App\Services\PaymentInitiationService;
use App\Services\SpesoClient;
use App\Ussd\States\ConfirmState;
use App\Ussd\States\MessageState;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Network;
use Sparors\Ussd\Action;
use Throwable;

/**
 * Confirms the ballot: creates the pending Payment and asks Speso to raise
 * the Mobile Money prompt. The vote itself is only credited when Speso's
 * webhook confirms the collection — see VoteCreditService.
 */
class PlaceVoteAction extends Action
{
    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));

        if ($input !== '1') {
            $this->record->set('final_message', 'Vote cancelled. Thank you for using CastVote.');

            return MessageState::class;
        }

        $event = Flow::event($this->record);

        if (! $event || ! $event->isLive()) {
            $this->record->set('final_message', 'Voting for this campaign has closed. Thank you.');

            return MessageState::class;
        }

        $phone      = (string) $this->record->get('phoneNumber');
        $nomineeId  = (int) $this->record->get('nominee_id');
        $categoryId = (int) $this->record->get('category_id');
        $quantity   = max(1, (int) $this->record->get('quantity', 1));
        $nominee    = (string) $this->record->get('nominee_name');

        if (! $nomineeId || ! $categoryId || $phone === '') {
            $this->record->set('error', 'Your session expired. Please start again.');

            return ConfirmState::class;
        }

        return $event->isPayPerVote()
            ? $this->collectPayment($event, $phone, $nomineeId, $categoryId, $quantity, $nominee)
            : $this->recordFreeVote($event, $phone, $nomineeId, $categoryId, $nominee);
    }

    private function collectPayment($event, string $phone, int $nomineeId, int $categoryId, int $quantity, string $nominee): string
    {
        $network = Network::resolve($this->record->get('network'), $phone);

        $payment = app(PaymentInitiationService::class)->createPendingPayment(
            event:         $event,
            phone:         $phone,
            nomineeId:     $nomineeId,
            categoryId:    $categoryId,
            quantity:      $quantity,
            channel:       'ussd',
            provider:      'speso',
            extraMetadata: array_filter([
                'eligible_voter_id' => $this->record->get('eligible_voter_id'),
            ]),
        );

        try {
            SpesoClient::make()->requestCollection(
                orderId:     $payment->provider_reference,
                mobile:      $phone,
                network:     $network,
                amount:      $payment->amount_pesewas / 100,
                description: "CastVote: {$quantity} vote(s) for {$nominee}",
            );
        } catch (Throwable $e) {
            report($e);

            // Leave the payment pending — ReverifyPendingPayments can still
            // settle it if the collection actually reached Speso.
            $this->record->set(
                'final_message',
                "Sorry, we couldn't reach the payment provider. Please try again shortly."
            );

            return MessageState::class;
        }

        $this->record->set('final_message',
            "Payment request sent!\n"
            .'Approve the Mobile Money prompt on '.$phone." to confirm your {$quantity} vote(s) for {$nominee}.\n"
            .'You will get an SMS once your vote is recorded.'
        );

        return MessageState::class;
    }

    /**
     * Free campaigns have nothing to collect, so the vote is recorded on the
     * spot rather than waiting on a payment webhook.
     */
    private function recordFreeVote($event, string $phone, int $nomineeId, int $categoryId, string $nominee): string
    {
        Vote::create([
            'event_id'    => $event->id,
            'category_id' => $categoryId,
            'nominee_id'  => $nomineeId,
            'quantity'    => 1,
            'channel'     => 'ussd',
            'voter_phone' => $event->isAnonymousTally() ? null : $phone,
            'payment_id'  => null,
        ]);

        if ($voterId = $this->record->get('eligible_voter_id')) {
            EligibleVoter::find($voterId)?->markVoted();
        }

        $this->record->set('final_message', "Your vote for {$nominee} has been recorded. Thank you!");

        return MessageState::class;
    }
}
