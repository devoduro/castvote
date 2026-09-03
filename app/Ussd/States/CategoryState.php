<?php

namespace App\Ussd\States;

use App\Models\Category;
use App\Ussd\Actions\SelectCategoryAction;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Text;
use Sparors\Ussd\State;

class CategoryState extends State
{
    protected function beforeRendering(): void
    {
        $categories = Category::where('event_id', $this->record->get('event_id'))
            ->orderBy('display_order')
            ->get(['id', 'name'])
            ->all();

        if ($categories === []) {
            $this->menu->text('No categories have been published for this campaign yet.');

            return;
        }

        $page = Flow::page($categories, (int) $this->record->get('page', 1));

        // The numbering the caller sees is the position in the full list, so
        // it stays stable as they page back and forth.
        $this->menu->text(Flow::takeError($this->record))->line('Select a category:');

        foreach ($page['items'] as $i => $category) {
            $this->menu->line(($page['offset'] + $i + 1).'. '.Text::truncate($category->name));
        }

        $this->menu->text(Flow::pagerHints($page['hasPrev'], $page['hasNext']));
    }

    protected function afterRendering(string $argument): void
    {
        $this->decision->any(SelectCategoryAction::class);
    }
}
