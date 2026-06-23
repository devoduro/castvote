<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Nominee;
use App\Models\Vote;
use Livewire\Attributes\Poll;
use Livewire\Component;

class GlobalVoteResults extends Component
{
    public string $selectedEventId = '';

    public function mount(): void
    {
        $first = Event::where('organization_id', auth('admin')->user()->organization_id)
            ->where('status', 'live')
            ->first()
            ?? Event::where('organization_id', auth('admin')->user()->organization_id)->latest()->first();

        $this->selectedEventId = (string) ($first?->id ?? '');
    }

    #[Poll(5000)]
    public function render()
    {
        $orgId  = auth('admin')->user()->organization_id;
        $events = Event::where('organization_id', $orgId)->orderByDesc('created_at')->get();

        $selectedEvent = $events->firstWhere('id', $this->selectedEventId);

        $results = [];
        if ($selectedEvent) {
            foreach ($selectedEvent->categories as $cat) {
                $nominees = Nominee::where('category_id', $cat->id)
                    ->get()
                    ->map(function ($nom) {
                        $nom->vote_total = (int) Vote::where('nominee_id', $nom->id)->sum('quantity');
                        return $nom;
                    })
                    ->sortByDesc('vote_total')
                    ->values();

                $total = $nominees->sum('vote_total');
                $results[] = ['category' => $cat, 'nominees' => $nominees, 'total' => $total];
            }
        }

        $totalVotes   = $selectedEvent ? Vote::where('event_id', $selectedEvent->id)->sum('quantity') : 0;
        $totalRevenue = $selectedEvent ? $selectedEvent->payments()->where('status', 'success')->sum('amount_pesewas') : 0;

        return view('livewire.admin.global-vote-results', compact(
            'events', 'selectedEvent', 'results', 'totalVotes', 'totalRevenue'
        ));
    }
}
