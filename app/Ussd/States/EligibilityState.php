<?php

namespace App\Ussd\States;

use App\Ussd\Actions\VerifyEligibilityAction;
use App\Ussd\Support\Flow;
use Sparors\Ussd\State;

/**
 * Restricted campaigns check the caller against the organiser's eligibility
 * list before a ballot is issued, matching the web ballot's gate.
 */
class EligibilityState extends State
{
    protected function beforeRendering(): void
    {
        $event = Flow::event($this->record);

        $label = match ($event?->event_type) {
            'election' => 'student index number',
            'agm'      => 'shareholder or member ID',
            default    => 'voter ID',
        };

        $this->menu
            ->text(Flow::takeError($this->record))
            ->line('Verify your eligibility')
            ->line("Enter your {$label}:")
            ->text(Flow::BACK.'. Back');
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision->any(VerifyEligibilityAction::class);
    }
}
