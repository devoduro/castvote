<?php

namespace App\Ussd\Actions;

use App\Models\EligibleVoter;
use App\Ussd\States\CategoryState;
use App\Ussd\States\EligibilityState;
use App\Ussd\States\MessageState;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

class VerifyEligibilityAction extends Action
{
    /** Wrong IDs are cheap to guess, so the session ends after this many. */
    private const MAX_ATTEMPTS = 3;

    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));

        if ($input === Flow::BACK) {
            return WelcomeState::class;
        }

        $voter = EligibleVoter::where('event_id', $this->record->get('event_id'))
            ->where('identifier', $input)
            ->first();

        if (! $voter || ! $voter->eligible) {
            return $this->reject('That ID is not on the eligible voters list.');
        }

        if ($voter->hasVoted()) {
            $this->record->set('final_message', 'Our records show this ID has already voted. Thank you.');

            return MessageState::class;
        }

        $this->record->setMultiple([
            'eligible_voter_id' => $voter->id,
            'page'              => 1,
            'eligibility_tries' => 0,
        ]);

        return CategoryState::class;
    }

    private function reject(string $message): string
    {
        $tries = (int) $this->record->get('eligibility_tries', 0) + 1;

        if ($tries >= self::MAX_ATTEMPTS) {
            $this->record->set('final_message', $message."\nPlease contact the event organiser for help.");

            return MessageState::class;
        }

        $this->record->setMultiple([
            'eligibility_tries' => $tries,
            'error'             => $message,
        ]);

        return EligibilityState::class;
    }
}
