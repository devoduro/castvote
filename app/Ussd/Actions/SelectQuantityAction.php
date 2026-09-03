<?php

namespace App\Ussd\Actions;

use App\Ussd\States\ConfirmState;
use App\Ussd\States\MessageState;
use App\Ussd\States\NomineeState;
use App\Ussd\States\QuantityState;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

class SelectQuantityAction extends Action
{
    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));

        if ($input === Flow::BACK) {
            $this->record->set('page', 1);

            return NomineeState::class;
        }

        $event = Flow::event($this->record);

        if (! $event) {
            $this->record->set('final_message', 'This voting campaign is no longer available. Thank you.');

            return MessageState::class;
        }

        $max = Flow::maxVotes($event);

        if (! ctype_digit($input) || (int) $input < 1 || (int) $input > $max) {
            $this->record->set('error', "Enter a number between 1 and {$max}.");

            return QuantityState::class;
        }

        $quantity = (int) $input;

        $this->record->setMultiple([
            'quantity'       => $quantity,
            'amount_pesewas' => $quantity * $event->pricePerVotePesewas(),
        ]);

        return ConfirmState::class;
    }
}
