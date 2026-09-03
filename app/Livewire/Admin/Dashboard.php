<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Vote;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $admin = auth('admin')->user();

        if ($admin->isSuperAdmin()) {
            return $this->superAdminData();
        }

        return $this->organizerData($admin);
    }

    private function organizerData($admin)
    {
        $orgId = $admin->organization_id;

        $events = Event::where('organization_id', $orgId)
            ->withCount('votes')
            ->latest()
            ->get();

        $grossRevenue  = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->where('status', 'success')->sum('amount_pesewas');
        $netRevenue    = (int) round($grossRevenue * 0.95);
        $totalVotes    = Vote::whereHas('event', fn($q) => $q->where('organization_id', $orgId))->sum('quantity');
        $pendingCount  = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->where('status', 'pending')->count();
        $liveEvents    = $events->where('status', 'live')->count();

        $todayVotes    = Vote::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->whereDate('created_at', today())->sum('quantity');

        $weekRevenue   = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('amount_pesewas');

        $recentPayments = Payment::whereHas('event', fn($q) => $q->where('organization_id', $orgId))
            ->with('event')
            ->latest()
            ->limit(6)
            ->get();

        $statusCounts = [
            'live'   => $events->where('status', 'live')->count(),
            'draft'  => $events->where('status', 'draft')->count(),
            'closed' => $events->where('status', 'closed')->count(),
        ];

        $ofOrg = fn ($q) => $q->where('organization_id', $orgId);

        // Votes and revenue for the last 14 days, gap-filled so the chart has
        // a bar for every day rather than skipping quiet ones.
        $voteRows = Vote::whereHas('event', $ofOrg)
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(quantity) as total')
            ->groupBy('day')->pluck('total', 'day');

        $revenueRows = Payment::whereHas('event', $ofOrg)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(amount_pesewas) as total')
            ->groupBy('day')->pluck('total', 'day');

        $trend = collect(range(13, 0))->map(function ($daysAgo) use ($voteRows, $revenueRows) {
            $date = now()->subDays($daysAgo);
            $key  = $date->toDateString();

            return (object) [
                'date'    => $date,
                'votes'   => (int) ($voteRows[$key] ?? 0),
                'revenue' => (int) ($revenueRows[$key] ?? 0),
            ];
        });

        $topNominees = Vote::whereHas('event', $ofOrg)
            ->with(['nominee', 'category'])
            ->selectRaw('nominee_id, category_id, SUM(quantity) as total')
            ->groupBy('nominee_id', 'category_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->filter(fn ($row) => $row->nominee !== null)
            ->values();

        $votesByCategory = Vote::whereHas('event', $ofOrg)
            ->with('category')
            ->selectRaw('category_id, SUM(quantity) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->filter(fn ($row) => $row->category !== null)
            ->values();

        return view('livewire.admin.dashboard', compact(
            'events', 'grossRevenue', 'netRevenue', 'totalVotes', 'pendingCount',
            'liveEvents', 'todayVotes', 'weekRevenue', 'recentPayments', 'statusCounts',
            'trend', 'topNominees', 'votesByCategory'
        ));
    }

    private function superAdminData()
    {
        $platformRevenue   = Payment::where('status', 'success')->sum('amount_pesewas');
        $platformFee       = (int) round($platformRevenue * 0.05);
        $totalOrgs         = Organization::count();
        $totalEvents       = Event::count();
        $liveEvents        = Event::where('status', 'live')->count();
        $totalVotes        = Vote::sum('quantity');
        $pendingApprovals  = Admin::where('is_superadmin', false)->where('account_status', 'pending')->count();
        $totalAdmins       = Admin::where('is_superadmin', false)->count();

        $orgs = Organization::withCount(['events', 'admins'])
            ->get()
            ->map(function ($org) {
                $org->revenue = Payment::whereHas('event', fn($q) => $q->where('organization_id', $org->id))
                    ->where('status', 'success')->sum('amount_pesewas');
                $org->votes   = Vote::whereHas('event', fn($q) => $q->where('organization_id', $org->id))->sum('quantity');
                $org->status  = Admin::where('organization_id', $org->id)->value('account_status') ?? 'unknown';
                return $org;
            })
            ->sortByDesc('revenue');

        $recentActivity = AuditLog::with('admin')
            ->latest()
            ->limit(10)
            ->get();

        $dailyRevenue = Payment::where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(amount_pesewas) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $recentOrgs = Admin::where('is_superadmin', false)
            ->with('organization')
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.admin.superadmin-dashboard', compact(
            'platformRevenue', 'platformFee', 'totalOrgs', 'totalEvents',
            'liveEvents', 'totalVotes', 'pendingApprovals', 'totalAdmins',
            'orgs', 'recentActivity', 'dailyRevenue', 'recentOrgs'
        ));
    }
}
