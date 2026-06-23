<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Vote;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $eventId     = '';
    public string $statusFilter = '';
    public string $channelFilter = '';

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingEventId(): void      { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function render()
    {
        $orgId  = auth('admin')->user()->organization_id;
        $events = Event::where('organization_id', $orgId)->orderBy('name')->get();

        $payments = Payment::with('event', 'vote.nominee')
            ->whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->when($this->eventId,      fn($q) => $q->where('event_id', $this->eventId))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->channelFilter, fn($q) => $q->whereHas('vote', fn($q2) => $q2->where('channel', $this->channelFilter)))
            ->when($this->search, fn($q) => $q->where(fn($q2) =>
                $q2->where('phone_number', 'like', "%{$this->search}%")
                   ->orWhere('provider_reference', 'like', "%{$this->search}%")
            ))
            ->latest()
            ->paginate(25);

        $grossRevenue  = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))->where('status', 'success')->sum('amount_pesewas');
        $totalVotes    = Vote::whereHas('event', fn($q) => $q->where('organization_id', $orgId))->sum('quantity');
        $totalTx       = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))->count();
        $pendingAmount = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))->where('status', 'pending')->count();

        return view('livewire.admin.transactions', compact(
            'payments', 'events', 'grossRevenue', 'totalVotes', 'totalTx', 'pendingAmount'
        ));
    }
}
