<?php

namespace App\Ussd\Actions;

use App\Models\Event;
use App\Ussd\States\CategoryState;
use App\Ussd\States\EligibilityState;
use App\Ussd\States\MessageState;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Campaign;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

class RouteWelcomeAction extends Action
{
    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));
        $event = Flow::event($this->record);

        // No campaign yet — the welcome screen was a picker, so this input
        // chooses one.
        if (! $event) {
            return $this->pickCampaign($input);
        }

        // '2' (My votes) is routed by WelcomeState straight to
        // ShowMyVotesAction: the machine runs exactly one Action between two
        // States, so an Action returning another Action is never rendered.
        return match ($input) {
            '1'     => $this->startVoting($event),
            '0'     => $this->goodbye(),
            default => $this->invalid(),
        };
    }

    private function pickCampaign(string $input): string
    {
        $page = (int) $this->record->get('page', 1);

        if ($input === Flow::BACK) {
            return $this->goodbye();
        }

        if ($input === Flow::NEXT || $input === Flow::PREV) {
            $this->record->set('page', $input === Flow::NEXT ? $page + 1 : max(1, $page - 1));

            return WelcomeState::class;
        }

        $campaigns = Campaign::liveCampaigns();

        $chosen = ctype_digit($input) && $campaigns->has((int) $input - 1)
            ? $campaigns[(int) $input - 1]
            : null;

        if (! $chosen) {
            $this->record->set('error', 'Invalid selection.');

            return WelcomeState::class;
        }

        $this->record->setMultiple(['event_id' => $chosen->id, 'page' => 1]);

        // Show that campaign's own menu next.
        return WelcomeState::class;
    }

    private function startVoting(Event $event): string
    {
        // A restricted campaign checks the caller against the organiser's
        // eligibility list before any ballot is shown. The web ballot gates
        // this the same way.
        if ($event->requiresEligibilityList() && ! $this->record->get('eligible_voter_id')) {
            return EligibilityState::class;
        }

        $this->record->set('page', 1);

        return CategoryState::class;
    }

    private function goodbye(): string
    {
        $this->record->set('final_message', 'Thank you for using ClickVote. Goodbye!');

        return MessageState::class;
    }

    private function invalid(): string
    {
        $this->record->set('error', 'Invalid choice.');

        return WelcomeState::class;
    }
}
