<?php

namespace App\Ussd\States;

use App\Ussd\Actions\RouteWelcomeAction;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Text;
use Sparors\Ussd\State;

/**
 * Entry screen. The campaign has already been resolved from the dialled
 * shortcode by the webhook controller, so this only has to offer the menu.
 */
class WelcomeState extends State
{
    protected function beforeRendering(): void
    {
        $event = Flow::event($this->record);

        $this->menu
            ->text(Flow::takeError($this->record))
            ->line(Text::truncate($event?->name ?? 'CastVote', 40))
            ->lineBreak()
            ->line('1. Vote')
            ->line('2. My votes')
            ->text('0. Exit');
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision->any(RouteWelcomeAction::class);
    }
}
