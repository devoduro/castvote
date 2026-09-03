<?php

namespace App\Ussd\Actions;

use App\Ussd\States\CategoryState;
use App\Ussd\States\EligibilityState;
use App\Ussd\States\MessageState;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

class RouteWelcomeAction extends Action
{
    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));
        $event = Flow::event($this->record);

        if (! $event) {
            $this->record->set('final_message', 'This voting campaign is no longer available. Thank you.');

            return MessageState::class;
        }

        return match ($input) {
            '1' => $this->startVoting($event),
            '2' => ShowMyVotesAction::class,
            '0' => $this->goodbye(),
            default => $this->invalid(),
        };
    }

    private function startVoting($event): string
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
        $this->record->set('final_message', 'Thank you for using CastVote. Goodbye!');

        return MessageState::class;
    }

    private function invalid(): string
    {
        $this->record->set('error', 'Invalid choice.');

        return WelcomeState::class;
    }
}
