<?php

namespace App\Ussd\States;

use App\Ussd\Actions\SelectQuantityAction;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Text;
use Sparors\Ussd\State;

class QuantityState extends State
{
    protected function beforeRendering(): void
    {
        $event = Flow::event($this->record);
        $max   = $event ? Flow::maxVotes($event) : 50;

        $this->menu
            ->text(Flow::takeError($this->record))
            ->line(Text::truncate((string) $this->record->get('nominee_name')))
            ->line('1 vote = GHS '.($event?->priceInGhs() ?? '0.00'))
            ->lineBreak()
            ->line("How many votes? (1-{$max})")
            ->text(Flow::BACK.'. Back');
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision->any(SelectQuantityAction::class);
    }
}
