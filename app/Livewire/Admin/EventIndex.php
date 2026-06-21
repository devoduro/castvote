<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class EventIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function toggleStatus(int $eventId): void
    {
        $admin = auth('admin')->user();
        $event = Event::where('organization_id', $admin->organization_id)->findOrFail($eventId);

        if (!$admin->isManager()) {
            $this->dispatch('notify', type: 'error', message: 'You do not have permission to change event status.');
            return;
        }

        $old = $event->status;
        $event->status = match ($old) {
            'draft'  => 'live',
            'live'   => 'closed',
            'closed' => 'closed',
        };
        $event->save();

        AuditLog::record('event.status_changed', $event, ['from' => $old, 'to' => $event->status]);
        $this->dispatch('notify', type: 'success', message: "Event status changed to {$event->status}.");
    }

    public function render()
    {
        $orgId = auth('admin')->user()->organization_id;

        $events = Event::where('organization_id', $orgId)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->withCount('votes')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.event-index', compact('events'));
    }
}
