<?php

namespace App\Ussd\Actions;

use App\Models\Payment;
use App\Models\Vote;
use App\Ussd\States\MessageState;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

/**
 * "My votes": what this handset has cast in the current campaign, plus any
 * payment still awaiting confirmation.
 */
class ShowMyVotesAction extends Action
{
    public function run(): string
    {
        $eventId = (int) $this->record->get('event_id');
        $phone   = (string) $this->record->get('phoneNumber');

        $votes = Vote::with('nominee')
            ->where('event_id', $eventId)
            ->where('voter_phone', $phone)
            ->latest()
            ->limit(3)
            ->get();

        $pending = Payment::where('event_id', $eventId)
            ->where('phone_number', $phone)
            ->where('status', 'pending')
            ->count();

        if ($votes->isEmpty()) {
            if ($pending > 0) {
                $this->record->set('final_message',
                    "No votes recorded yet.\n{$pending} payment(s) still being confirmed. You'll get an SMS once they clear.");

                return MessageState::class;
            }

            // Don't invite a caller to vote on a ballot that has shut.
            $closed = Flow::event($this->record)?->votingHasClosed();

            $this->record->set('final_message', $closed
                ? 'You did not cast any votes in this campaign. Voting has now closed.'
                : "You have not cast any votes in this campaign yet.\nDial again and choose 1 to vote.");

            return MessageState::class;
        }

        $lines = ['Your recent votes:'];

        foreach ($votes as $vote) {
            $lines[] = "{$vote->quantity} x ".($vote->nominee?->name ?? 'nominee')
                .' on '.$vote->created_at->format('d M');
        }

        if ($pending > 0) {
            $lines[] = "{$pending} payment(s) still confirming.";
        }

        $this->record->set('final_message', implode("\n", $lines));

        return MessageState::class;
    }
}
