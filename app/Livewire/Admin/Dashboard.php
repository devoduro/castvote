<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $admin = auth('admin')->user();
        $orgId = $admin->organization_id;

        $events = Event::where('organization_id', $orgId)
            ->withCount('votes')
            ->latest()
            ->get();

        $totalVotes    = Vote::whereHas('event', fn($q) => $q->where('organization_id', $orgId))->sum('quantity');
        $totalRevenue  = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->where('status', 'success')->sum('amount_pesewas');
        $pendingCount  = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->where('status', 'pending')->count();
        $liveEvents    = $events->where('status', 'live')->count();

        return view('livewire.admin.dashboard', compact(
            'events', 'totalVotes', 'totalRevenue', 'pendingCount', 'liveEvents'
        ));
    }
}
