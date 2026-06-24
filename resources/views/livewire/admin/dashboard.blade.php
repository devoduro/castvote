<div>

@php
    $admin    = auth('admin')->user();
    $orgName  = $admin->organization?->name ?? 'Your Organization';
    $hour     = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $gRev     = $grossRevenue / 100;
    $nRev     = $netRevenue / 100;
    $wRev     = $weekRevenue / 100;
@endphp

{{-- ══════════════════════════════════
     PAGE HEADER
══════════════════════════════════ --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:22px;flex-wrap:wrap">
    <div>
        <h1 style="font-size:22px;font-weight:900;color:#1e293b;line-height:1.2">Dashboard</h1>
        <p style="font-size:13px;color:#94a3b8;margin-top:3px">
            {{ $greeting }}, <strong style="color:#475569">{{ $admin->name }}</strong> &mdash; {{ now()->format('l, d F Y') }}
        </p>
    </div>
    <a href="{{ route('admin.events.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#4361ee;color:white;font-size:13px;font-weight:700;padding:10px 18px;border-radius:10px;text-decoration:none;box-shadow:0 4px 12px rgba(67,97,238,.3);flex-shrink:0">
        <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Create Event
    </a>
</div>

{{-- ══════════════════════════════════
     KPI CARDS — Solid colored, watermark icon style
══════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px">

    @php
    $kpis = [
        [
            'label'  => 'Total Votes',
            'value'  => number_format($totalVotes),
            'sub'    => number_format($todayVotes).' votes today',
            'bg'     => '#4361ee',
            'shadow' => 'rgba(67,97,238,.3)',
            'path'   => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label'  => 'Gross Revenue',
            'value'  => 'GH₵ '.number_format($gRev, 0),
            'sub'    => 'GH₵ '.number_format($wRev, 0).' this week',
            'bg'     => '#22c55e',
            'shadow' => 'rgba(34,197,94,.3)',
            'path'   => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label'  => 'Net Earnings',
            'value'  => 'GH₵ '.number_format($nRev, 0),
            'sub'    => 'After 5% platform fee',
            'bg'     => '#f97316',
            'shadow' => 'rgba(249,115,22,.3)',
            'path'   => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
        ],
        [
            'label'  => 'Live Events',
            'value'  => $liveEvents,
            'sub'    => $events->count().' events total',
            'bg'     => '#7c3aed',
            'shadow' => 'rgba(124,58,237,.3)',
            'path'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        ],
    ];
    @endphp

    @foreach($kpis as $k)
    <div style="background:{{ $k['bg'] }};border-radius:16px;padding:22px;position:relative;overflow:hidden;box-shadow:0 6px 20px {{ $k['shadow'] }}">
        {{-- Watermark icon --}}
        <div style="position:absolute;bottom:-12px;right:-10px;opacity:.18;pointer-events:none">
            <svg style="width:88px;height:88px;color:white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['path'] }}"/>
            </svg>
        </div>

        <div style="position:relative;z-index:1">
            <p style="font-size:clamp(26px,3vw,34px);font-weight:900;color:white;line-height:1;margin-bottom:6px">{{ $k['value'] }}</p>
            <p style="font-size:13px;font-weight:700;color:rgba(255,255,255,.9);margin-bottom:4px">{{ $k['label'] }}</p>
            <p style="font-size:11.5px;color:rgba(255,255,255,.6);font-weight:500">{{ $k['sub'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════
     ROW 2: Charts side by side
══════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:280px 1fr;gap:16px;margin-bottom:22px;align-items:start">

    {{-- Event Status donut --}}
    <div style="background:white;border-radius:16px;border:1px solid #e8eaf0;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:22px">
        <h2 style="font-size:13.5px;font-weight:800;color:#1e293b;margin-bottom:18px">Event Status</h2>

        @php
            $total = max($events->count(), 1);
            $lPct  = round($statusCounts['live'] / $total * 100);
            $dPct  = round($statusCounts['draft'] / $total * 100);
        @endphp

        <div style="display:flex;justify-content:center;margin-bottom:20px">
            <div style="position:relative;width:120px;height:120px">
                <div style="width:120px;height:120px;border-radius:50%;background:conic-gradient(
                    #4361ee 0% {{ $lPct }}%,
                    #f97316 {{ $lPct }}% {{ $lPct + $dPct }}%,
                    #e2e8f0 {{ $lPct + $dPct }}% 100%
                )"></div>
                <div style="position:absolute;inset:16px;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;flex-direction:column">
                    <p style="font-size:22px;font-weight:900;color:#1e293b;line-height:1">{{ $events->count() }}</p>
                    <p style="font-size:9.5px;font-weight:600;color:#94a3b8">Total</p>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:9px">
            @foreach([
                ['Live', $statusCounts['live'] ?? 0, '#4361ee'],
                ['Draft', $statusCounts['draft'] ?? 0, '#f97316'],
                ['Closed', $statusCounts['closed'] ?? 0, '#e2e8f0'],
            ] as [$lbl, $cnt, $clr])
            <div style="display:flex;align-items:center;gap:9px">
                <span style="width:10px;height:10px;border-radius:3px;background:{{ $clr }};flex-shrink:0"></span>
                <span style="font-size:12.5px;color:#374151;flex:1;font-weight:500">{{ $lbl }}</span>
                <span style="font-size:14px;font-weight:800;color:#1e293b">{{ $cnt }}</span>
                <span style="font-size:10.5px;color:#94a3b8;width:30px;text-align:right">{{ $total > 0 ? round($cnt/$total*100) : 0 }}%</span>
            </div>
            @endforeach
        </div>

        @if($pendingCount > 0)
        <div style="margin-top:14px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:9px 12px;display:flex;align-items:center;gap:7px">
            <svg style="width:13px;height:13px;color:#f97316;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p style="font-size:12px;color:#9a3412;font-weight:600">{{ $pendingCount }} payment{{ $pendingCount !== 1 ? 's' : '' }} pending</p>
        </div>
        @endif
    </div>

    {{-- Revenue by event --}}
    <div style="background:white;border-radius:16px;border:1px solid #e8eaf0;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:22px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
                <h2 style="font-size:13.5px;font-weight:800;color:#1e293b">Revenue by Event</h2>
                <p style="font-size:12px;color:#94a3b8;margin-top:2px">Top events by revenue</p>
            </div>
            <a href="{{ route('admin.earnings') }}" style="font-size:12px;font-weight:700;color:#4361ee;text-decoration:none;background:#f0f4ff;padding:5px 12px;border-radius:8px">View All</a>
        </div>

        @php
            $chartEvents = $events->map(function($e) {
                $e->chart_rev = \App\Models\Payment::where('event_id',$e->id)->where('status','success')->sum('amount_pesewas');
                return $e;
            })->sortByDesc('chart_rev')->take(6);
            $maxRev = $chartEvents->max('chart_rev') ?: 1;
            $barColors = ['#4361ee','#22c55e','#f97316','#7c3aed','#06b6d4','#ef4444'];
        @endphp

        @if($chartEvents->isEmpty())
        <div style="padding:28px;text-align:center;color:#94a3b8;font-size:13px">No events with revenue yet.</div>
        @else
        <div style="display:flex;flex-direction:column;gap:13px">
            @foreach($chartEvents as $i => $ce)
            @php $pct = round($ce->chart_rev / $maxRev * 100); $clr = $barColors[$i % 6]; @endphp
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:5px;gap:12px">
                    <div style="display:flex;align-items:center;gap:8px;min-width:0;flex:1">
                        <span style="width:10px;height:10px;border-radius:3px;background:{{ $clr }};flex-shrink:0"></span>
                        <span style="font-size:12.5px;font-weight:600;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Str::limit($ce->name, 28) }}</span>
                    </div>
                    <span style="font-size:13px;font-weight:800;color:#1e293b;flex-shrink:0">GH₵ {{ number_format($ce->chart_rev/100, 0) }}</span>
                </div>
                <div style="height:8px;background:#f1f5f9;border-radius:8px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $clr }};border-radius:8px"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top:18px;padding-top:14px;border-top:1px solid #f1f5f9;display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
            @foreach([
                ['Gross','GH₵ '.number_format($gRev,0),'#f0f4ff','#4361ee'],
                ['Net','GH₵ '.number_format($nRev,0),'#f0fdf4','#22c55e'],
                ['This Week','GH₵ '.number_format($wRev,0),'#fff7ed','#f97316'],
            ] as [$l,$v,$bg,$tc])
            <div style="background:{{ $bg }};border-radius:10px;padding:10px 12px">
                <p style="font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px">{{ $l }}</p>
                <p style="font-size:14.5px;font-weight:900;color:{{ $tc }}">{{ $v }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════
     ROW 3: Events table + right panel
══════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 272px;gap:16px;align-items:start">

    {{-- Events table --}}
    <div style="background:white;border-radius:16px;border:1px solid #e8eaf0;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden">
        <div style="padding:16px 20px 0;border-bottom:1px solid #f1f5f9">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                <h2 style="font-size:13.5px;font-weight:800;color:#1e293b">Events Report</h2>
                <a href="{{ route('admin.events.index') }}" style="font-size:12px;font-weight:700;color:white;background:#4361ee;padding:6px 14px;border-radius:8px;text-decoration:none">+ New Event</a>
            </div>
            <div style="display:flex;gap:0">
                @foreach(['all'=>'All','live'=>'Live','draft'=>'Draft','closed'=>'Closed'] as $val => $label)
                <button onclick="filterEv('{{ $val }}')" id="evt-{{ $val }}"
                        style="font-size:12px;font-weight:600;padding:7px 14px;border:none;background:none;cursor:pointer;border-bottom:2px solid {{ $val === 'all' ? '#4361ee' : 'transparent' }};margin-bottom:-1px;color:{{ $val === 'all' ? '#4361ee' : '#94a3b8' }};transition:all .15s">
                    {{ $label }}
                    <span style="font-size:10px;font-weight:700;background:{{ $val === 'all' ? '#e8efff' : '#f1f5f9' }};color:{{ $val === 'all' ? '#4361ee' : '#94a3b8' }};padding:1px 6px;border-radius:20px;margin-left:2px">
                        {{ $val === 'all' ? $events->count() : ($statusCounts[$val] ?? 0) }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        @if($events->isEmpty())
        <div style="padding:44px;text-align:center">
            <div style="width:52px;height:52px;background:#e8efff;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg style="width:24px;height:24px;color:#4361ee" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p style="font-size:14px;font-weight:700;color:#374151;margin-bottom:5px">No events yet</p>
            <p style="font-size:12.5px;color:#94a3b8;margin-bottom:16px">Create your first voting event to get started.</p>
            <a href="{{ route('admin.events.create') }}" style="background:#4361ee;color:white;font-size:13px;font-weight:700;padding:9px 22px;border-radius:9px;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                + Create Event
            </a>
        </div>
        @else
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="font-size:10.5px;font-weight:700;color:#94a3b8;letter-spacing:.06em;text-transform:uppercase;padding:10px 20px;text-align:left;border-bottom:1px solid #f1f5f9">Event</th>
                    <th style="font-size:10.5px;font-weight:700;color:#94a3b8;letter-spacing:.06em;text-transform:uppercase;padding:10px 12px;text-align:center;border-bottom:1px solid #f1f5f9">Status</th>
                    <th style="font-size:10.5px;font-weight:700;color:#94a3b8;letter-spacing:.06em;text-transform:uppercase;padding:10px 12px;text-align:right;border-bottom:1px solid #f1f5f9">Votes</th>
                    <th style="font-size:10.5px;font-weight:700;color:#94a3b8;letter-spacing:.06em;text-transform:uppercase;padding:10px 20px;text-align:right;border-bottom:1px solid #f1f5f9">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events->take(10) as $event)
                @php
                    $evRev = \App\Models\Payment::where('event_id',$event->id)->where('status','success')->sum('amount_pesewas') / 100;
                    $sStyle = match($event->status) {
                        'live'  => ['bg'=>'#dcfce7','color'=>'#15803d','dot'=>'#22c55e','label'=>'Live'],
                        'draft' => ['bg'=>'#f1f5f9','color'=>'#64748b','dot'=>'#94a3b8','label'=>'Draft'],
                        default => ['bg'=>'#fee2e2','color'=>'#dc2626','dot'=>'#ef4444','label'=>'Closed'],
                    };
                    $iconBg = match($event->status) {
                        'live'  => '#e8efff', 'draft' => '#f1f5f9', default => '#fee2e2',
                    };
                    $iconColor = match($event->status) {
                        'live'  => '#4361ee', 'draft' => '#94a3b8', default => '#ef4444',
                    };
                @endphp
                <tr class="ev-r" data-status="{{ $event->status }}"
                    style="border-bottom:1px solid #f8fafc;cursor:pointer;transition:background .1s"
                    onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background='white'">
                    <td style="padding:12px 20px">
                        <div style="display:flex;align-items:center;gap:11px">
                            <div style="width:35px;height:35px;border-radius:10px;background:{{ $iconBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg style="width:16px;height:16px;color:{{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#1e293b;line-height:1.2">{{ Str::limit($event->name, 30) }}</p>
                                <p style="font-size:11px;color:#94a3b8;margin-top:1px">{{ $event->start_date ? $event->start_date->format('M j, Y') : 'No date' }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px;text-align:center">
                        <span style="display:inline-flex;align-items:center;gap:4px;background:{{ $sStyle['bg'] }};color:{{ $sStyle['color'] }};font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $sStyle['dot'] }}"></span>
                            {{ $sStyle['label'] }}
                        </span>
                    </td>
                    <td style="padding:12px;text-align:right;font-size:13px;font-weight:700;color:#374151">{{ number_format($event->votes_count) }}</td>
                    <td style="padding:12px 20px;text-align:right;font-size:13px;font-weight:800;color:#1e293b">GH₵ {{ number_format($evRev, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($events->count() > 10)
        <div style="padding:13px 20px;border-top:1px solid #f1f5f9;text-align:center">
            <a href="{{ route('admin.events.index') }}" style="font-size:13px;font-weight:700;color:#4361ee;text-decoration:none">See all {{ $events->count() }} events →</a>
        </div>
        @endif
        @endif
    </div>

    {{-- Right panel --}}
    <div style="display:flex;flex-direction:column;gap:14px">

        {{-- Quick Actions (colored buttons matching reference) --}}
        <div style="background:white;border-radius:16px;border:1px solid #e8eaf0;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:20px">
            <h3 style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:13px">Quick Actions</h3>
            <div style="display:flex;flex-direction:column;gap:8px">
                @foreach([
                    ['Create Event',     route('admin.events.create'), '#4361ee'],
                    ['View Nominations', route('admin.nominations'),   '#7c3aed'],
                    ['Vote Results',     route('admin.vote-results'),  '#f97316'],
                    ['Earnings',         route('admin.earnings'),      '#22c55e'],
                ] as [$lbl,$href,$clr])
                <a href="{{ $href }}"
                   style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;background:{{ $clr }};text-decoration:none;transition:opacity .15s;color:white;position:relative;overflow:hidden"
                   onmouseover="this.style.opacity='.92'" onmouseout="this.style.opacity='1'">
                    <svg style="width:14px;height:14px;flex-shrink:0;opacity:.85" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span style="font-size:12.5px;font-weight:700">{{ $lbl }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Recent Payments --}}
        @if($recentPayments->isNotEmpty())
        <div style="background:white;border-radius:16px;border:1px solid #e8eaf0;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:13px">
                <h3 style="font-size:13px;font-weight:800;color:#1e293b">Recent Payments</h3>
                <a href="{{ route('admin.transactions') }}" style="font-size:11.5px;font-weight:700;color:#4361ee;text-decoration:none">View all</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
                @foreach($recentPayments->take(5) as $pay)
                @php
                    $pclr = match($pay->status) {
                        'success' => ['#dcfce7','#15803d'],
                        'pending' => ['#fef3c7','#b45309'],
                        default   => ['#fee2e2','#dc2626'],
                    };
                    $picon = match($pay->status) {
                        'success' => '#22c55e',
                        'pending' => '#f97316',
                        default   => '#ef4444',
                    };
                @endphp
                <div style="display:flex;align-items:center;gap:9px">
                    <div style="width:32px;height:32px;border-radius:9px;background:{{ $pclr[0] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:14px;height:14px;color:{{ $picon }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:12px;font-weight:700;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ Str::limit($pay->event?->name ?? 'Unknown', 20) }}</p>
                        <p style="font-size:10.5px;color:#94a3b8">{{ $pay->created_at->diffForHumans() }}</p>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <p style="font-size:12.5px;font-weight:800;color:#1e293b">GH₵ {{ number_format($pay->amount_pesewas/100, 2) }}</p>
                        <span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:8px;background:{{ $pclr[0] }};color:{{ $pclr[1] }}">{{ strtoupper($pay->status) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<script>
function filterEv(status) {
    ['all','live','draft','closed'].forEach(function(s) {
        var btn = document.getElementById('evt-'+s);
        var on  = s === status;
        btn.style.borderBottomColor = on ? '#4361ee' : 'transparent';
        btn.style.color = on ? '#4361ee' : '#94a3b8';
        var badge = btn.querySelector('span');
        if (badge) {
            badge.style.background = on ? '#e8efff' : '#f1f5f9';
            badge.style.color = on ? '#4361ee' : '#94a3b8';
        }
    });
    document.querySelectorAll('.ev-r').forEach(function(r) {
        r.style.display = (status === 'all' || r.dataset.status === status) ? '' : 'none';
    });
}
</script>

</div>
