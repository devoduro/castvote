<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Payment;
use Livewire\Component;

class Earnings extends Component
{
    public string $eventFilter = '';

    public function render()
    {
        $orgId  = auth('admin')->user()->organization_id;
        $events = Event::where('organization_id', $orgId)->orderBy('name')->get();

        $query = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->when($this->eventFilter, fn($q) => $q->where('event_id', $this->eventFilter));

        $grossRevenue  = (clone $query)->where('status', 'success')->sum('amount_pesewas');
        $platformFee   = (int) round($grossRevenue * 0.05);   // 5% platform commission
        $netRevenue    = $grossRevenue - $platformFee;

        // Payments over time (last 30 days, by day)
        $dailyRevenue = (clone $query)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as day, SUM(amount_pesewas) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Per-event breakdown
        $eventBreakdown = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->where('status', 'success')
            ->selectRaw('event_id, SUM(amount_pesewas) as total, COUNT(*) as tx_count')
            ->groupBy('event_id')
            ->with('event:id,name,status')
            ->get();

        return view('livewire.admin.earnings', compact(
            'events', 'grossRevenue', 'platformFee', 'netRevenue', 'dailyRevenue', 'eventBreakdown'
        ));
    }
}
