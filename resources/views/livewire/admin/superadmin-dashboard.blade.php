<div>
    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:28px">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                <div style="background:linear-gradient(135deg,#e91e8c,#7c3aed);color:white;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.07em">PLATFORM ADMIN</div>
            </div>
            <h1 style="font-size:24px;font-weight:900;color:#1a0030;margin-bottom:4px;letter-spacing:-.3px">Platform Overview</h1>
            <p style="color:#9ca3af;font-size:14px">Real-time monitoring across all organizations and events.</p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('admin.approvals') }}"
               style="display:inline-flex;align-items:center;gap:7px;background:white;border:1.5px solid #e5e7eb;color:#1a0030;border-radius:12px;padding:10px 18px;font-size:13.5px;font-weight:700;text-decoration:none">
                @if($pendingApprovals > 0)
                <span style="background:#e91e8c;color:white;width:18px;height:18px;border-radius:50%;font-size:10px;font-weight:800;display:inline-flex;align-items:center;justify-content:center">{{ $pendingApprovals }}</span>
                @endif
                Approvals
            </a>
            <a href="{{ route('admin.audit') }}"
               style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border-radius:12px;padding:10px 18px;font-size:13.5px;font-weight:700;text-decoration:none">
                Audit Log
            </a>
        </div>
    </div>

    {{-- Platform KPI cards --}}
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:28px">
        @foreach([
            ['PLATFORM REVENUE',  'GHS '.number_format($platformRevenue/100,2), '#e91e8c'],
            ['PLATFORM FEE (5%)', 'GHS '.number_format($platformFee/100,2),     '#7c3aed'],
            ['ORGANIZATIONS',     $totalOrgs,                                    '#2d0050'],
            ['TOTAL EVENTS',      $totalEvents,                                  '#059669'],
            ['LIVE NOW',          $liveEvents,                                   '#16a34a'],
            ['TOTAL VOTES',       number_format($totalVotes),                    '#d97706'],
        ] as [$label, $val, $color])
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:18px;position:relative;overflow:hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $color }}"></div>
            <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">{{ $label }}</p>
            <p style="font-size:20px;font-weight:900;color:#1a0030">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Pending approvals alert --}}
    @if($pendingApprovals > 0)
    <div style="background:linear-gradient(135deg,#fff7ed,#fffbeb);border:1.5px solid #fde68a;border-radius:14px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:12px">
        <div style="width:36px;height:36px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg style="width:18px;height:18px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div style="flex:1">
            <p style="font-weight:700;color:#92400e;font-size:14px;margin-bottom:2px">{{ $pendingApprovals }} organizer account{{ $pendingApprovals > 1 ? 's' : '' }} pending approval</p>
            <p style="font-size:13px;color:#d97706">Review and approve/reject to allow organizers to publish events.</p>
        </div>
        <a href="{{ route('admin.approvals') }}"
           style="background:#d97706;color:white;font-size:13px;font-weight:700;padding:9px 18px;border-radius:10px;text-decoration:none;white-space:nowrap">
            Review Now →
        </a>
    </div>
    @endif

    {{-- Main grid: Organizations table + Revenue chart --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px">

        {{-- Organizations table --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
            <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
                <h2 style="font-size:15px;font-weight:800;color:#1a0030">All Organizations</h2>
                <span style="color:#9ca3af;font-size:13px">{{ $orgs->count() }} registered</span>
            </div>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                            @foreach(['Organization','Admin','Events','Votes','Revenue','Status'] as $h)
                            <th style="padding:10px 16px;text-align:left;font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orgs as $org)
                        @php
                            $sc=['approved'=>['#d1fae5','#059669'],'pending'=>['#fef3c7','#d97706'],'rejected'=>['#fee2e2','#dc2626']];
                            [$sbg,$stc]=$sc[$org->status]??['#f3f4f6','#6b7280'];
                        @endphp
                        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                            <td style="padding:12px 16px">
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#e91e8c15,#7c3aed15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:700;color:#7c3aed">
                                        {{ strtoupper(substr($org->name,0,2)) }}
                                    </div>
                                    <div>
                                        <p style="font-weight:700;color:#1a0030;font-size:13px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $org->name }}</p>
                                        <p style="color:#9ca3af;font-size:11px">{{ $org->contact_email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:12px 16px;font-size:13px;color:#374151">{{ $org->admins_count }}</td>
                            <td style="padding:12px 16px;font-size:13px;color:#374151">{{ $org->events_count }}</td>
                            <td style="padding:12px 16px;font-family:monospace;font-size:13px;font-weight:600;color:#1a0030">{{ number_format($org->votes) }}</td>
                            <td style="padding:12px 16px;font-family:monospace;font-size:13px;font-weight:700;color:#059669">GHS {{ number_format($org->revenue/100,2) }}</td>
                            <td style="padding:12px 16px">
                                <span style="background:{{ $sbg }};color:{{ $stc }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase">{{ ucfirst($org->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding:40px;text-align:center;color:#9ca3af;font-size:14px">No organizations yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Revenue trend (30-day bar chart) --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px">
            <h2 style="font-size:15px;font-weight:800;color:#1a0030;margin-bottom:4px">Revenue Trend</h2>
            <p style="font-size:12px;color:#9ca3af;margin-bottom:20px">Last 30 days</p>

            @if($dailyRevenue->isEmpty())
            <div style="display:flex;align-items:center;justify-content:center;height:140px;color:#9ca3af;font-size:13px">No revenue data yet.</div>
            @else
            @php $maxRev = $dailyRevenue->max('total') ?: 1; @endphp
            <div style="display:flex;align-items:flex-end;gap:3px;height:120px;padding-bottom:4px">
                @foreach($dailyRevenue as $day)
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px"
                     title="{{ $day->date }}: GHS {{ number_format($day->total/100,2) }}">
                    <div style="width:100%;border-radius:4px 4px 0 0;background:linear-gradient(180deg,#e91e8c,#7c3aed);min-height:3px;height:{{ round($day->total/$maxRev*110) }}px;transition:height .3s"></div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10.5px;color:#9ca3af;margin-top:8px">
                <span>{{ \Carbon\Carbon::parse($dailyRevenue->first()->date)->format('d M') }}</span>
                <span>Today</span>
            </div>
            @endif

            {{-- Summary --}}
            <div style="border-top:1px solid #f3f4f6;margin-top:16px;padding-top:16px;display:flex;justify-content:space-between">
                <div>
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:.06em;margin-bottom:4px">PLATFORM FEE</p>
                    <p style="font-size:18px;font-weight:900;color:#e91e8c">GHS {{ number_format($platformFee/100,2) }}</p>
                </div>
                <div style="text-align:right">
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:.06em;margin-bottom:4px">TOTAL ADMINS</p>
                    <p style="font-size:18px;font-weight:900;color:#1a0030">{{ $totalAdmins }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent signups + Recent activity --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">

        {{-- Recent Organizers --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
            <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
                <h2 style="font-size:15px;font-weight:800;color:#1a0030">Recent Signups</h2>
                <a href="{{ route('admin.approvals') }}" style="font-size:13px;font-weight:700;color:#e91e8c;text-decoration:none">View all →</a>
            </div>
            @forelse($recentOrgs as $org)
            @php
                $sc=['approved'=>['#d1fae5','#059669'],'pending'=>['#fef3c7','#d97706'],'rejected'=>['#fee2e2','#dc2626']];
                [$sbg,$stc]=$sc[$org->account_status]??['#f3f4f6','#6b7280'];
            @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:13px 20px;{{ !$loop->last?'border-bottom:1px solid #f9fafb;':'' }}"
                 onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#e91e8c,#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr($org->name,0,2)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <p style="font-weight:700;color:#1a0030;font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $org->name }}</p>
                    <p style="color:#9ca3af;font-size:11.5px">{{ $org->organization?->name ?? '—' }} · {{ $org->created_at->diffForHumans() }}</p>
                </div>
                <span style="background:{{ $sbg }};color:{{ $stc }};padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;flex-shrink:0">{{ $org->account_status }}</span>
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:#9ca3af;font-size:13.5px">No signups yet.</div>
            @endforelse
        </div>

        {{-- Recent Activity --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
            <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
                <h2 style="font-size:15px;font-weight:800;color:#1a0030">Recent Activity</h2>
                <a href="{{ route('admin.audit') }}" style="font-size:13px;font-weight:700;color:#e91e8c;text-decoration:none">Full log →</a>
            </div>
            @php
                $actionColors=['account'=>['#dbeafe','#1d4ed8'],'event'=>['#f3e8ff','#7c3aed'],'payment'=>['#d1fae5','#059669'],'organizer'=>['#fef3c7','#d97706'],'vote'=>['#fce7f3','#be185d'],'password'=>['#fee2e2','#dc2626'],'profile'=>['#e0f2fe','#0369a1']];
            @endphp
            @forelse($recentActivity as $log)
            @php
                $prefix=explode('.',$log->action)[0];
                [$lbg,$ltc]=$actionColors[$prefix]??['#f3f4f6','#6b7280'];
            @endphp
            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 20px;{{ !$loop->last?'border-bottom:1px solid #f9fafb;':'' }}"
                 onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                <div style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,#e91e8c,#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">
                    {{ strtoupper(substr($log->admin?->name ?? '?',0,2)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:2px">
                        <span style="background:{{ $lbg }};color:{{ $ltc }};padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700">{{ $log->action }}</span>
                    </div>
                    <p style="font-size:11.5px;color:#6b7280">{{ $log->admin?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:#9ca3af;font-size:13.5px">No activity yet.</div>
            @endforelse
        </div>
    </div>
</div>
