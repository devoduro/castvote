<?php

namespace App\Ussd\States;

use App\Models\Nominee;
use App\Ussd\Actions\SelectNomineeAction;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Text;
use Sparors\Ussd\State;

class NomineeState extends State
{
    protected function beforeRendering(): void
    {
        $nominees = Nominee::where('category_id', $this->record->get('category_id'))
            ->orderBy('display_order')
            ->get(['id', 'name', 'code'])
            ->all();

        if ($nominees === []) {
            $this->menu
                ->line('No nominees in this category yet.')
                ->text(Flow::BACK.'. Back');

            return;
        }

        $page = Flow::page($nominees, (int) $this->record->get('page', 1));

        $this->menu
            ->text(Flow::takeError($this->record))
            ->line(Text::truncate((string) $this->record->get('category_name')))
            ->line('Select a nominee:');

        foreach ($page['items'] as $i => $nominee) {
            $this->menu->line(($page['offset'] + $i + 1).'. '.Text::truncate($nominee->name, 22).' ('.$nominee->code.')');
        }

        $this->menu->text(Flow::pagerHints($page['hasPrev'], $page['hasNext']));
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision->any(SelectNomineeAction::class);
    }
}
