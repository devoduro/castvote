<?php

namespace App\Ussd\Actions;

use App\Models\Nominee;
use App\Ussd\States\CategoryState;
use App\Ussd\States\ConfirmState;
use App\Ussd\States\MessageState;
use App\Ussd\States\NomineeState;
use App\Ussd\States\QuantityState;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

class SelectNomineeAction extends Action
{
    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));
        $page  = (int) $this->record->get('page', 1);

        if ($input === Flow::BACK) {
            $this->record->set('page', 1);

            return CategoryState::class;
        }

        if ($input === Flow::NEXT || $input === Flow::PREV) {
            $this->record->set('page', $input === Flow::NEXT ? $page + 1 : max(1, $page - 1));

            return NomineeState::class;
        }

        $nominees = Nominee::where('category_id', $this->record->get('category_id'))
            ->orderBy('display_order')
            ->get(['id', 'name', 'code']);

        $nominee = ctype_digit($input) && $nominees->has((int) $input - 1)
            ? $nominees[(int) $input - 1]
            : $nominees->firstWhere('code', $input);

        if (! $nominee) {
            $this->record->set('error', 'Invalid selection.');

            return NomineeState::class;
        }

        $event = Flow::event($this->record);

        if (! $event) {
            $this->record->set('final_message', 'This voting campaign is no longer available. Thank you.');

            return MessageState::class;
        }

        $this->record->setMultiple([
            'nominee_id'   => $nominee->id,
            'nominee_name' => $nominee->name,
            'nominee_code' => $nominee->code,
        ]);

        // A free campaign has nothing to price, so skip straight to confirmation.
        if (! $event->isPayPerVote()) {
            $this->record->setMultiple(['quantity' => 1, 'amount_pesewas' => 0]);

            return ConfirmState::class;
        }

        return QuantityState::class;
    }
}
