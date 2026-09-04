<?php

namespace App\Ussd\States;

use App\Ussd\Actions\RouteWelcomeAction;
use App\Ussd\Actions\ShowMyVotesAction;
use App\Ussd\Support\Campaign;
use App\Ussd\Support\Flow;
use App\Ussd\Support\Text;
use Sparors\Ussd\State;

/**
 * Entry screen.
 *
 * When the gateway identified the campaign — a dedicated shortcode, or a Nalo
 * USERID matching an event — this is that campaign's own menu. On a shared
 * code where nothing identifies it, this becomes a picker instead.
 */
class WelcomeState extends State
{
    protected function beforeRendering(): void
    {
        $event = Flow::event($this->record);

        if (! $event) {
            $this->renderPicker();

            return;
        }

        $this->menu
            ->text(Flow::takeError($this->record))
            ->line(Text::truncate($event->name, 34))
            ->line('1. Vote')
            ->line('2. My votes')
            ->text('0. Exit');
    }

    private function renderPicker(): void
    {
        $campaigns = Campaign::liveCampaigns()->all();

        if ($campaigns === []) {
            $this->menu->text('No voting campaign is open right now. Please try again later.');

            return;
        }

        $page = Flow::page($campaigns, (int) $this->record->get('page', 1));

        $this->menu->text(Flow::takeError($this->record))->line('Select an award:');

        foreach ($page['items'] as $i => $campaign) {
            $this->menu->line(($page['offset'] + $i + 1).'. '.Text::truncate($campaign->name, 24));
        }

        $this->menu->text(Flow::pagerHints($page['hasPrev'], $page['hasNext'], 'Exit'));
    }

    protected function afterRendering(string $argument): void
    {
        // The machine runs exactly one Action between two States — an Action
        // returning another Action is never rendered — so "My votes" is
        // routed straight from here rather than via RouteWelcomeAction.
        // Only when a campaign is already chosen: in picker mode '2' means
        // the second award on the list.
        if ($this->record->get('event_id')) {
            $this->decision->equal('2', ShowMyVotesAction::class);
        }

        $this->decision->any(RouteWelcomeAction::class);
    }
}
