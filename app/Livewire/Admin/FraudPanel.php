<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Vote;
use Livewire\Component;

class FraudPanel extends Component
{
    public Event $event;

    // Threshold: flag phones that cast more votes than this in 10 minutes
    public int $burstThreshold = 50;
    public int $windowMinutes  = 10;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function render()
    {
        // High-volume voters: same phone, unusually high total vote quantity
        $highVolume = Vote::selectRaw('voter_phone, SUM(quantity) as total_votes, COUNT(*) as transactions, MAX(created_at) as last_vote')
            ->where('event_id', $this->event->id)
            ->whereNotNull('voter_phone')
            ->groupBy('voter_phone')
            ->havingRaw('SUM(quantity) > ?', [200])
            ->orderByDesc('total_votes')
            ->limit(50)
            ->get();

        // Burst voters: many transactions in a short window (possible bot)
        $burstVoters = Vote::selectRaw(
            'voter_phone,
             COUNT(*) as tx_count,
             SUM(quantity) as total_votes,
             MIN(created_at) as first_tx,
             MAX(created_at) as last_tx,
             TIMESTAMPDIFF(MINUTE, MIN(created_at), MAX(created_at)) as span_minutes'
        )
            ->where('event_id', $this->event->id)
            ->whereNotNull('voter_phone')
            ->where('created_at', '>=', now()->subHours(24))
            ->groupBy('voter_phone')
            ->havingRaw('COUNT(*) >= ?', [$this->burstThreshold])
            ->havingRaw('TIMESTAMPDIFF(MINUTE, MIN(created_at), MAX(created_at)) <= ?', [$this->windowMinutes])
            ->orderByDesc('tx_count')
            ->limit(50)
            ->get();

        // Channel breakdown
        $channelBreakdown = Vote::selectRaw('channel, SUM(quantity) as total, COUNT(*) as transactions')
            ->where('event_id', $this->event->id)
            ->groupBy('channel')
            ->get();

        // Hourly vote trend (last 48 hours)
        $hourlyTrend = Vote::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00") as hour, SUM(quantity) as votes')
            ->where('event_id', $this->event->id)
            ->where('created_at', '>=', now()->subHours(48))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('livewire.admin.fraud-panel', compact(
            'highVolume', 'burstVoters', 'channelBreakdown', 'hourlyTrend'
        ));
    }
}
