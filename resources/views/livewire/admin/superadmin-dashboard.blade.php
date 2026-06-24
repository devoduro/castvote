<div>

    {{-- ══ Page Header ══ --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#1e293b;letter-spacing:-.4px;line-height:1.2">Platform Overview</h1>
            <p style="color:#94a3b8;font-size:12.5px;margin-top:3px">{{ now()->format('l, d F Y') }} &bull; Real-time monitoring across all organizations.</p>
        </div>
        <div style="display:flex;gap:9px;flex-wrap:wrap;align-items:center">
            @if($liveEvents > 0)
            <div style="display:flex;align-items:center;gap:6px;background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:8px;padding:6px 12px">
                <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse2 2s infinite;flex-shrink:0"></span>
                <span style="font-size:12px;font-weight:700;color:#15803d">{{ $liveEvents }} Live</span>
            </div>
            @endif
            <a href="{{ route('admin.approvals') }}"
               style="display:inline-flex;align-items:center;gap:6px;background:white;border:1.5px solid #e2e8f0;color:#475569;border-radius:9px;padding:8px 14px;font-size:12.5px;font-weight:700;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#4361ee';this.style.color='#4361ee'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569'">
                @if($pendingApprovals > 0)
                <span style="background:#ef4444;color:white;width:17px;height:17px;border-radius:50%;font-size:10px;font-weight:800;display:inline-flex;align-items:center;justify-content:center">{{ $pendingApprovals }}</span>
                @endif
                Approvals
            </a>
            <a href="{{ route('admin.audit') }}"
               style="display:inline-flex;align-items:center;gap:6px;background:#4361ee;color:white;border-radius:9px;padding:8px 14px;font-size:12.5px;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(67,97,238,.28)">
                <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Audit Log
            </a>
        </div>
    </div>

    {{-- ══ KPI Cards — 6 compact in single row ══ --}}
    @php
        $kpis = [
            ['label'=>'Platform Revenue', 'value'=>'GHS '.number_format($platformRevenue/100,2), 'sub'=>'Total collected',    'bg'=>'#4361ee','shadow'=>'rgba(67,97,238,.25)',  'path'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Platform Fee 5%',  'value'=>'GHS '.number_format($platformFee/100,2),    'sub'=>'Our earnings',       'bg'=>'#22c55e','shadow'=>'rgba(34,197,94,.25)',   'path'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['label'=>'Organizations',    'value'=>$totalOrgs,                                   'sub'=>'Registered',         'bg'=>'#f97316','shadow'=>'rgba(249,115,22,.25)', 'path'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['label'=>'Total Events',     'value'=>$totalEvents,                                 'sub'=>'All time',           'bg'=>'#7c3aed','shadow'=>'rgba(124,58,237,.25)', 'path'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'Live Now',         'value'=>$liveEvents,                                  'sub'=>'Active events',      'bg'=>'#22c55e','shadow'=>'rgba(34,197,94,.25)',  'path'=>'M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M8.464 15.536a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M12 12h.01'],
            ['label'=>'Total Votes',      'value'=>number_format($totalVotes),                   'sub'=>'Platform-wide',      'bg'=>'#4361ee','shadow'=>'rgba(67,97,238,.25)',  'path'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ];
    @endphp

    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:18px">
        @foreach($kpis as $k)
        <div style="background:{{ $k['bg'] }};border-radius:12px;padding:16px 15px 14px;position:relative;overflow:hidden;box-shadow:0 4px 14px {{ $k['shadow'] }}">
            <div style="position:absolute;bottom:-10px;right:-8px;opacity:.16;pointer-events:none">
                <svg style="width:68px;height:68px;color:white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['path'] }}"/>
                </svg>
            </div>
            <div style="position:relative;z-index:1">
                <p style="font-size:19px;font-weight:900;color:white;letter-spacing:-.4px;line-height:1.15;margin-bottom:5px">{{ $k['value'] }}</p>
                <p style="font-size:11.5px;font-weight:700;color:rgba(255,255,255,.9);margin-bottom:1px">{{ $k['label'] }}</p>
                <p style="font-size:10.5px;color:rgba(255,255,255,.55)">{{ $k['sub'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══ Platform Status Bar ══ --}}
    <div style="background:white;border-radius:11px;border:1px solid #e8eaf0;padding:11px 18px;margin-bottom:18px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 1px 4px rgba(0,0,0,.04)">
        <div style="display:flex;align-items:center;gap:7px">
            <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0;animation:pulse2 2s infinite"></span>
            <span style="font-size:12.5px;font-weight:700;color:#1e293b">Platform healthy</span>
        </div>
        <div style="width:1px;height:16px;background:#e8eaf0;flex-shrink:0"></div>
        @if($pendingApprovals > 0)
        <a href="{{ route('admin.approvals') }}" style="display:flex;align-items:center;gap:6px;text-decoration:none">
            <svg style="width:13px;height:13px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span style="font-size:12px;color:#d97706;font-weight:700">{{ $pendingApprovals }} pending approval{{ $pendingApprovals > 1 ? 's' : '' }} — Review</span>
        </a>
        @else
        <div style="display:flex;align-items:center;gap:6px">
            <svg style="width:13px;height:13px;color:#22c55e" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span style="font-size:12px;color:#64748b;font-weight:600">No pending approvals</span>
        </div>
        @endif
        <div style="width:1px;height:16px;background:#e8eaf0;flex-shrink:0"></div>
        <div style="display:flex;align-items:center;gap:6px">
            <svg style="width:13px;height:13px;color:#64748b" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span style="font-size:12px;color:#64748b;font-weight:600">{{ $totalAdmins }} organizer admins across {{ $totalOrgs }} orgs</span>
        </div>
        <div style="margin-left:auto;font-size:11px;color:#b0b7c3;font-weight:500">Last updated: just now</div>
    </div>

    {{-- ══ Main Grid ══ --}}
    <div style="display:grid;grid-template-columns:1fr 308px;gap:18px;margin-bottom:18px">

        {{-- Organizations Table --}}
        <div style="background:white;border-radius:14px;border:1px solid #e8eaf0;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid #f0f2f5;display:flex;align-items:center;justify-content:space-between;gap:12px">
                <div>
                    <h2 style="font-size:14px;font-weight:800;color:#1e293b">All Organizations</h2>
                    <p style="font-size:11.5px;color:#94a3b8;margin-top:1px">{{ $orgs->count() }} registered &bull; sorted by revenue</p>
                </div>
                <a href="{{ route('admin.approvals') }}"
                   style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#4361ee;text-decoration:none;background:rgba(67,97,238,.08);padding:6px 12px;border-radius:7px"
                   onmouseover="this.style.background='rgba(67,97,238,.14)'" onmouseout="this.style.background='rgba(67,97,238,.08)'">
                    <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Manage
                </a>
            </div>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f0f2f5">
                            @foreach(['Organization','Admins','Events','Votes','Revenue','Status'] as $h)
                            <th style="padding:8px 16px;text-align:left;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxRevOrg = $orgs->max('revenue') ?: 1; @endphp
                        @forelse($orgs as $org)
                        @php
                            $sc=['approved'=>['#dcfce7','#059669'],'pending'=>['#fef3c7','#d97706'],'rejected'=>['#fee2e2','#dc2626']];
                            [$sbg,$stc]=$sc[$org->status]??['#f3f4f6','#6b7280'];
                            $revPct = $maxRevOrg > 0 ? round($org->revenue / $maxRevOrg * 100) : 0;
                        @endphp
                        <tr style="border-top:1px solid #f5f7fa;transition:background .1s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <td style="padding:11px 16px">
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div style="width:30px;height:30px;border-radius:8px;background:#4361ee;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10.5px;font-weight:800;color:white;letter-spacing:-.5px">
                                        {{ strtoupper(substr($org->name,0,2)) }}
                                    </div>
                                    <div style="min-width:0">
                                        <p style="font-weight:700;color:#1e293b;font-size:12.5px;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $org->name }}</p>
                                        <p style="color:#94a3b8;font-size:10.5px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:150px">{{ $org->contact_email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:11px 16px;font-size:12.5px;color:#475569;font-weight:600">{{ $org->admins_count }}</td>
                            <td style="padding:11px 16px;font-size:12.5px;color:#475569;font-weight:600">{{ $org->events_count }}</td>
                            <td style="padding:11px 16px;font-size:12.5px;font-weight:700;color:#1e293b">{{ number_format($org->votes) }}</td>
                            <td style="padding:11px 16px;min-width:110px">
                                <p style="font-size:12.5px;font-weight:700;color:#22c55e;white-space:nowrap;margin-bottom:3px">GHS {{ number_format($org->revenue/100,2) }}</p>
                                <div style="height:3px;border-radius:3px;background:#f0f2f5;overflow:hidden">
                                    <div style="height:100%;width:{{ $revPct }}%;background:#22c55e;border-radius:3px;transition:width .4s"></div>
                                </div>
                            </td>
                            <td style="padding:11px 16px">
                                <span style="background:{{ $sbg }};color:{{ $stc }};padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap">{{ ucfirst($org->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:48px;text-align:center">
                                <svg style="width:32px;height:32px;color:#d1d5db;margin:0 auto 10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                                <p style="color:#94a3b8;font-size:13.5px;font-weight:600">No organizations yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right Panel --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Revenue Card with Chart --}}
            <div style="background:white;border-radius:14px;border:1px solid #e8eaf0;box-shadow:0 2px 8px rgba(0,0,0,.04);padding:16px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px">
                    <div>
                        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px">Platform Revenue</p>
                        <p style="font-size:20px;font-weight:900;color:#1e293b;letter-spacing:-.5px">GHS {{ number_format($platformRevenue/100,2) }}</p>
                    </div>
                    <div style="background:rgba(67,97,238,.08);border-radius:8px;padding:6px 10px;text-align:center">
                        <p style="font-size:10px;font-weight:700;color:#4361ee;margin-bottom:1px">FEE</p>
                        <p style="font-size:13px;font-weight:900;color:#4361ee">GHS {{ number_format($platformFee/100,2) }}</p>
                    </div>
                </div>

                @if($dailyRevenue->isEmpty())
                <div style="height:72px;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:9px">
                    <p style="color:#94a3b8;font-size:12px">No revenue data yet</p>
                </div>
                @else
                @php $maxRev = $dailyRevenue->max('total') ?: 1; @endphp
                <div style="display:flex;align-items:flex-end;gap:2px;height:72px;border-radius:9px;overflow:hidden;background:#f8fafc;padding:8px 8px 0">
                    @foreach($dailyRevenue as $day)
                    <div style="flex:1;border-radius:2px 2px 0 0;background:linear-gradient(180deg,#4361ee,#7c3aed);min-height:2px;height:{{ max(2, round($day->total/$maxRev*58)) }}px;transition:height .3s;cursor:default"
                         title="{{ $day->date }}: GHS {{ number_format($day->total/100,2) }}"
                         onmouseover="this.style.opacity='.65'" onmouseout="this.style.opacity='1'"></div>
                    @endforeach
                </div>
                <div style="display:flex;justify-content:space-between;font-size:10px;color:#b0b7c3;margin-top:5px;font-weight:600">
                    <span>{{ \Carbon\Carbon::parse($dailyRevenue->first()->date)->format('d M') }}</span>
                    <span>Last 30 days</span>
                    <span>Today</span>
                </div>
                @endif

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-top:12px;padding-top:12px;border-top:1px solid #f0f2f5">
                    <div style="background:rgba(124,58,237,.07);border-radius:8px;padding:9px 10px">
                        <p style="font-size:9.5px;font-weight:700;color:#7c3aed;letter-spacing:.06em;margin-bottom:3px">TOTAL ADMINS</p>
                        <p style="font-size:16px;font-weight:900;color:#7c3aed">{{ $totalAdmins }}</p>
                    </div>
                    <div style="background:rgba(249,115,22,.07);border-radius:8px;padding:9px 10px">
                        <p style="font-size:9.5px;font-weight:700;color:#f97316;letter-spacing:.06em;margin-bottom:3px">TOTAL ORGS</p>
                        <p style="font-size:16px;font-weight:900;color:#f97316">{{ $totalOrgs }}</p>
                    </div>
                </div>
            </div>

            {{-- Recent Signups --}}
            <div style="background:white;border-radius:14px;border:1px solid #e8eaf0;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden;flex:1">
                <div style="padding:12px 16px;border-bottom:1px solid #f0f2f5;display:flex;align-items:center;justify-content:space-between">
                    <h3 style="font-size:13px;font-weight:800;color:#1e293b">Recent Signups</h3>
                    <a href="{{ route('admin.approvals') }}" style="font-size:11.5px;font-weight:700;color:#4361ee;text-decoration:none">View all →</a>
                </div>
                @forelse($recentOrgs as $org)
                @php
                    $sc=['approved'=>['#dcfce7','#059669'],'pending'=>['#fef3c7','#d97706'],'rejected'=>['#fee2e2','#dc2626']];
                    [$sbg,$stc]=$sc[$org->account_status]??['#f3f4f6','#6b7280'];
                    $avatarColors=['#4361ee','#22c55e','#f97316','#7c3aed','#0ea5e9'];
                    $ac=$avatarColors[$loop->index % count($avatarColors)];
                @endphp
                <div style="display:flex;align-items:center;gap:9px;padding:10px 16px;{{ !$loop->last?'border-bottom:1px solid #f5f7fa;':'' }};transition:background .1s"
                     onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <div style="width:30px;height:30px;border-radius:8px;background:{{ $ac }};display:flex;align-items:center;justify-content:center;color:white;font-size:10px;font-weight:800;flex-shrink:0;letter-spacing:-.5px">
                        {{ strtoupper(substr($org->organization?->name ?? $org->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-weight:700;color:#1e293b;font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $org->organization?->name ?? $org->name }}</p>
                        <p style="color:#94a3b8;font-size:10.5px">{{ $org->created_at->diffForHumans() }}</p>
                    </div>
                    <span style="background:{{ $sbg }};color:{{ $stc }};padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:700;text-transform:uppercase;flex-shrink:0;white-space:nowrap">{{ $org->account_status }}</span>
                </div>
                @empty
                <div style="padding:28px 16px;text-align:center">
                    <p style="color:#94a3b8;font-size:13px">No signups yet.</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- ══ Recent Activity — Timeline ══ --}}
    <div style="background:white;border-radius:14px;border:1px solid #e8eaf0;box-shadow:0 2px 8px rgba(0,0,0,.04);overflow:hidden">
        <div style="padding:14px 20px;border-bottom:1px solid #f0f2f5;display:flex;align-items:center;justify-content:space-between">
            <div>
                <h2 style="font-size:14px;font-weight:800;color:#1e293b">Recent Activity</h2>
                <p style="font-size:11.5px;color:#94a3b8;margin-top:1px">Latest platform audit events</p>
            </div>
            <a href="{{ route('admin.audit') }}"
               style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#4361ee;text-decoration:none;background:rgba(67,97,238,.08);padding:6px 12px;border-radius:7px"
               onmouseover="this.style.background='rgba(67,97,238,.14)'" onmouseout="this.style.background='rgba(67,97,238,.08)'">
                Full log →
            </a>
        </div>
        @php
            $actionMeta = [
                'account'   => ['#dbeafe','#1d4ed8','bg-blue'],
                'event'     => ['rgba(124,58,237,.12)','#7c3aed','bg-purple'],
                'payment'   => ['#d1fae5','#059669','bg-green'],
                'organizer' => ['#fef3c7','#d97706','bg-yellow'],
                'vote'      => ['rgba(67,97,238,.1)','#4361ee','bg-indigo'],
                'password'  => ['#fee2e2','#dc2626','bg-red'],
                'profile'   => ['#e0f2fe','#0369a1','bg-cyan'],
            ];
            $dotColors = ['account'=>'#3b82f6','event'=>'#7c3aed','payment'=>'#22c55e','organizer'=>'#f59e0b','vote'=>'#4361ee','password'=>'#ef4444','profile'=>'#0ea5e9'];
        @endphp
        @if($recentActivity->isEmpty())
        <div style="padding:48px;text-align:center;color:#94a3b8;font-size:13.5px">No activity yet.</div>
        @else
        <div style="padding:6px 20px 4px;display:grid;grid-template-columns:repeat(2,1fr);gap:0">
            @foreach($recentActivity as $log)
            @php
                $prefix=explode('.',$log->action)[0];
                [$lbg,$ltc]=$actionMeta[$prefix]??['#f3f4f6','#6b7280'];
                $dot=$dotColors[$prefix]??'#94a3b8';
            @endphp
            <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 10px 10px {{ $loop->index % 2 === 0 ? '0' : '14px' }};{{ !$loop->last?'border-bottom:1px solid #f5f7fa;':'' }}{{ $loop->index % 2 === 0 ? 'border-right:1px solid #f5f7fa;padding-right:14px;' : '' }};transition:background .1s"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                {{-- Avatar --}}
                <div style="width:28px;height:28px;border-radius:7px;background:{{ $lbg }};display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:{{ $ltc }};flex-shrink:0;margin-top:1px;letter-spacing:-.5px">
                    {{ strtoupper(substr($log->admin?->name ?? '?', 0, 2)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:2px">
                        <span style="background:{{ $lbg }};color:{{ $ltc }};padding:1.5px 7px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap">{{ $log->action }}</span>
                    </div>
                    <p style="font-size:11.5px;color:#64748b">
                        <span style="font-weight:600;color:#475569">{{ $log->admin?->name ?? 'System' }}</span>
                        &bull; {{ $log->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
