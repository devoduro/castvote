<?php

namespace App\Ussd\States;

use App\Ussd\Actions\PlaceVoteAction;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Text;
use Sparors\Ussd\State;

class ConfirmState extends State
{
    protected function beforeRendering(): void
    {
        $event    = Flow::event($this->record);
        $quantity = (int) $this->record->get('quantity', 1);
        $amount   = (int) $this->record->get('amount_pesewas', 0);

        $this->menu
            ->text(Flow::takeError($this->record))
            ->line('Confirm your vote')
            ->line(Text::truncate((string) $this->record->get('nominee_name')))
            ->line('Category: '.Text::truncate((string) $this->record->get('category_name'), 24));

        if ($event?->isPayPerVote()) {
            $this->menu
                ->line("Votes: {$quantity}")
                ->line('Total: GHS '.number_format($amount / 100, 2));
        } else {
            $this->menu->line('Votes: 1 (free)');
        }

        $this->menu
            ->lineBreak()
            ->line('1. Confirm')
            ->text(Flow::BACK.'. Cancel');
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision->any(PlaceVoteAction::class);
    }
}
