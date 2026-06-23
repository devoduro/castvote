<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Nominee;
use App\Models\Vote;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalNominations extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $eventId  = '';

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingEventId(): void { $this->resetPage(); }

    public function render()
    {
        $orgId  = auth('admin')->user()->organization_id;
        $events = Event::where('organization_id', $orgId)->orderBy('name')->get();

        $nominees = Nominee::with(['category.event'])
            ->whereHas('category.event', fn($q) => $q->where('organization_id', $orgId))
            ->when($this->eventId, fn($q) => $q->whereHas('category.event', fn($q2) => $q2->where('id', $this->eventId)))
            ->when($this->search,  fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('votes')
            ->orderBy('name')
            ->paginate(20);

        $counts = [
            'total'    => Nominee::whereHas('category.event', fn($q) => $q->where('organization_id', $orgId))->count(),
            'approved' => Nominee::whereHas('category.event', fn($q) => $q->where('organization_id', $orgId))->count(),
            'pending'  => 0,
            'rejected' => 0,
        ];

        return view('livewire.admin.global-nominations', compact('nominees', 'events', 'counts'));
    }
}
