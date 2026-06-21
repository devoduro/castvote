<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Vote;
use Livewire\Attributes\Poll;
use Livewire\Component;

class ResultsDashboard extends Component
{
    public Event $event;
    public int   $selectedCategoryId = 0;

    public function mount(Event $event): void
    {
        $this->event              = $event;
        $this->selectedCategoryId = $event->categories()->value('id') ?? 0;
    }

    public function toggleResultsVisibility(): void
    {
        $admin = auth('admin')->user();
        if (!$admin->isManager()) return;

        $current = $this->event->voting_rules;
        $current['results_public'] = !($current['results_public'] ?? false);
        $this->event->update(['voting_rules' => $current]);

        AuditLog::record('event.results_visibility_changed', $this->event, [
            'results_public' => $current['results_public'],
        ]);
    }

    #[Poll(2500)]   // Livewire 4: polls every 2.5 seconds
    public function render()
    {
        $categories = $this->event->categories()->withCount('votes')->get();

        $results = [];
        foreach ($categories as $cat) {
            $nominees = Nominee::where('category_id', $cat->id)
                ->select('id', 'name', 'photo_path')
                ->get()
                ->map(function ($nom) {
                    $nom->vote_count = Vote::where('nominee_id', $nom->id)->sum('quantity');
                    return $nom;
                })
                ->sortByDesc('vote_count')
                ->values();

            $total = $nominees->sum('vote_count');

            $results[$cat->id] = [
                'category' => $cat,
                'nominees' => $nominees,
                'total'    => $total,
            ];
        }

        $selectedResult = $results[$this->selectedCategoryId] ?? reset($results) ?: null;

        $totalRevenue = $this->event->payments()
            ->where('status', 'success')
            ->sum('amount_pesewas');

        return view('livewire.admin.results-dashboard', compact(
            'categories', 'results', 'selectedResult', 'totalRevenue'
        ));
    }
}
