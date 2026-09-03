<?php

namespace App\Ussd\Actions;

use App\Models\Category;
use App\Ussd\States\CategoryState;
use App\Ussd\States\NomineeState;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Flow;
use Sparors\Ussd\Action;

class SelectCategoryAction extends Action
{
    public function run(): string
    {
        $input = trim((string) $this->record->get('input'));
        $page  = (int) $this->record->get('page', 1);

        if ($input === Flow::BACK) {
            return WelcomeState::class;
        }

        if ($input === Flow::NEXT || $input === Flow::PREV) {
            $this->record->set('page', $input === Flow::NEXT ? $page + 1 : max(1, $page - 1));

            return CategoryState::class;
        }

        $categories = Category::where('event_id', $this->record->get('event_id'))
            ->orderBy('display_order')
            ->get(['id', 'name', 'code']);

        // Accept the position number shown on screen, or the category's own
        // code as printed on the campaign's flyers.
        $category = ctype_digit($input) && $categories->has((int) $input - 1)
            ? $categories[(int) $input - 1]
            : $categories->firstWhere('code', $input);

        if (! $category) {
            $this->record->set('error', 'Invalid selection.');

            return CategoryState::class;
        }

        $this->record->setMultiple([
            'category_id'   => $category->id,
            'category_name' => $category->name,
            'page'          => 1,
        ]);

        return NomineeState::class;
    }
}
