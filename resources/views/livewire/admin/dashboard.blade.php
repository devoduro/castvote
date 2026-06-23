<div x-data="{ tab: 'all' }">

    {{-- ══ Page Header ══ --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap">
        <div>
            <p style="font-size:13px;color:#9ca3af;font-weight:500;margin-bottom:3px">
                {{ now()->format('l, d F Y') }}
            </p>
            <h1 style="font-size:22px;font-weight:900;color:#1a0030;letter-spacing:-.4px;line-height:1.2">
                Welcome back, {{ Str::words(auth('admin')->user()->name, 1, '') }} 👋
            </h1>
            <p style="color:#9ca3af;font-size:13px;margin-top:2px">Here's what's happening across your events today.</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            @if($liveEvents > 0)
            <div style="display:flex;align-items:center;gap:7px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:8px 14px">
                <span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block;animation:softpulse 2s infinite"></span>
                <span style="font-size:13px;font-weight:700;color:#166534">{{ $liveEvents }} Live Event{{ $liveEvents > 1 ? 's' : '' }}</span>
            </div>
            @endif
            <a href="{{ route('admin.events.create') }}"
               style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#e91e8c,#ad1070);color:white;border-radius:12px;padding:10px 20px;font-size:13.5px;font-weight:700;text-decoration:none;white-space:nowrap;box-shadow:0 4px 14px rgba(233,30,140,.35)">
                <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Create Event
            </a>
        </div>
    </div>

    {{-- ══ KPI Cards ══ --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px">

        {{-- Gross Revenue --}}
        <div style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(79,70,229,.3)">
            <div style="position:absolute;top:-20px;right:-20px;width:90px;height:90px;background:rgba(255,255,255,.08);border-radius:50%"></div>
            <div style="position:absolute;bottom:-30px;right:10px;width:60px;height:60px;background:rgba(255,255,255,.05);border-radius:50%"></div>
            <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:11px;font-weight:700;letter-spacing:.08em;opacity:.75;margin-bottom:6px">GROSS REVENUE</p>
            <p style="font-size:24px;font-weight:900;letter-spacing:-.5px;margin-bottom:10px">GHS {{ number_format($grossRevenue/100,2) }}</p>
            <div style="display:flex;align-items:center;gap:5px;border-top:1px solid rgba(255,255,255,.15);padding-top:10px">
                <svg style="width:13px;height:13px;opacity:.8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span style="font-size:11px;opacity:.8;font-weight:600">This week: GHS {{ number_format($weekRevenue/100,2) }}</span>
            </div>
        </div>

        {{-- Net Earnings --}}
        <div style="background:linear-gradient(135deg,#059669 0%,#10b981 100%);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(5,150,105,.3)">
            <div style="position:absolute;top:-20px;right:-20px;width:90px;height:90px;background:rgba(255,255,255,.08);border-radius:50%"></div>
            <div style="position:absolute;bottom:-30px;right:10px;width:60px;height:60px;background:rgba(255,255,255,.05);border-radius:50%"></div>
            <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p style="font-size:11px;font-weight:700;letter-spacing:.08em;opacity:.75;margin-bottom:6px">NET EARNINGS</p>
            <p style="font-size:24px;font-weight:900;letter-spacing:-.5px;margin-bottom:10px">GHS {{ number_format($netRevenue/100,2) }}</p>
            <div style="display:flex;align-items:center;gap:5px;border-top:1px solid rgba(255,255,255,.15);padding-top:10px">
                <svg style="width:13px;height:13px;opacity:.8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                </svg>
                <span style="font-size:11px;opacity:.8;font-weight:600">After 5% platform fee</span>
            </div>
        </div>

        {{-- Total Votes --}}
        <div style="background:linear-gradient(135deg,#e91e8c 0%,#ad1070 100%);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(233,30,140,.3)">
            <div style="position:absolute;top:-20px;right:-20px;width:90px;height:90px;background:rgba(255,255,255,.08);border-radius:50%"></div>
            <div style="position:absolute;bottom:-30px;right:10px;width:60px;height:60px;background:rgba(255,255,255,.05);border-radius:50%"></div>
            <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p style="font-size:11px;font-weight:700;letter-spacing:.08em;opacity:.75;margin-bottom:6px">TOTAL VOTES</p>
            <p style="font-size:24px;font-weight:900;letter-spacing:-.5px;margin-bottom:10px">{{ number_format($totalVotes) }}</p>
            <div style="display:flex;align-items:center;gap:5px;border-top:1px solid rgba(255,255,255,.15);padding-top:10px">
                <svg style="width:13px;height:13px;opacity:.8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span style="font-size:11px;opacity:.8;font-weight:600">Today: {{ number_format($todayVotes) }} votes</span>
            </div>
        </div>

        {{-- Events Overview --}}
        <div style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);border-radius:18px;padding:22px;color:white;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(245,158,11,.3)">
            <div style="position:absolute;top:-20px;right:-20px;width:90px;height:90px;background:rgba(255,255,255,.08);border-radius:50%"></div>
            <div style="position:absolute;bottom:-30px;right:10px;width:60px;height:60px;background:rgba(255,255,255,.05);border-radius:50%"></div>
            <div style="width:40px;height:40px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p style="font-size:11px;font-weight:700;letter-spacing:.08em;opacity:.75;margin-bottom:6px">TOTAL EVENTS</p>
            <p style="font-size:24px;font-weight:900;letter-spacing:-.5px;margin-bottom:10px">{{ $events->count() }}</p>
            <div style="display:flex;align-items:center;gap:10px;border-top:1px solid rgba(255,255,255,.15);padding-top:10px">
                <span style="font-size:11px;opacity:.8;font-weight:600">{{ $statusCounts['live'] }} live</span>
                <span style="opacity:.4">·</span>
                <span style="font-size:11px;opacity:.8;font-weight:600">{{ $statusCounts['draft'] }} draft</span>
                <span style="opacity:.4">·</span>
                <span style="font-size:11px;opacity:.8;font-weight:600">{{ $statusCounts['closed'] }} closed</span>
            </div>
        </div>

    </div>

    {{-- ══ Main 2-col grid ══ --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:22px;align-items:start">

        {{-- ── Left: Events Panel ── --}}
        <div>
            {{-- Panel header with tabs --}}
            <div style="background:white;border-radius:18px;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden">

                <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 20px 0">
                    <h2 style="font-size:15px;font-weight:800;color:#1a0030">Your Events</h2>
                    <a href="{{ route('admin.events.index') }}"
                       style="font-size:12.5px;font-weight:700;color:#e91e8c;text-decoration:none">
                        View All →
                    </a>
                </div>

                {{-- Tabs --}}
                <div style="display:flex;gap:4px;padding:14px 20px 0;border-bottom:1px solid #f3f4f6">
                    @foreach([
                        ['all',    'All',    $events->count()],
                        ['live',   'Live',   $statusCounts['live']],
                        ['draft',  'Draft',  $statusCounts['draft']],
                        ['closed', 'Closed', $statusCounts['closed']],
                    ] as [$key, $label, $count])
                    <button @click="tab = '{{ $key }}'"
                            :style="tab === '{{ $key }}'
                                ? 'border-bottom:2.5px solid #e91e8c;color:#e91e8c;background:transparent;'
                                : 'border-bottom:2.5px solid transparent;color:#9ca3af;background:transparent;'"
                            style="display:flex;align-items:center;gap:6px;padding:8px 14px;font-size:13px;font-weight:700;border:none;border-left:none;border-right:none;border-top:none;cursor:pointer;transition:color .15s;margin-bottom:-1px">
                        {{ $label }}
                        <span :style="tab === '{{ $key }}'
                                  ? 'background:#fce7f3;color:#e91e8c;'
                                  : 'background:#f3f4f6;color:#9ca3af;'"
                              style="font-size:10.5px;font-weight:800;padding:1px 7px;border-radius:20px">{{ $count }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Events list --}}
                @if($events->isEmpty())
                <div style="padding:60px 20px;text-align:center">
                    <div style="width:56px;height:56px;background:#f3f4f6;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                        <svg style="width:26px;height:26px;color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p style="font-weight:700;color:#374151;font-size:15px;margin-bottom:6px">No events yet</p>
                    <p style="color:#9ca3af;font-size:13px;margin-bottom:18px">Create your first event to see performance here.</p>
                    <a href="{{ route('admin.events.create') }}"
                       style="display:inline-flex;align-items:center;gap:6px;background:#1a0030;color:white;font-size:13px;font-weight:700;padding:10px 20px;border-radius:10px;text-decoration:none">
                        Create Event
                    </a>
                </div>
                @else

                @foreach($events->take(8) as $event)
                @php
                    $rev   = \App\Models\Payment::where('event_id',$event->id)->where('status','success')->sum('amount_pesewas');
                    $cats  = $event->categories()->count();
                    $noms  = \App\Models\Nominee::whereHas('category', fn($q) => $q->where('event_id',$event->id))->count();
                    $sc    = ['live' => ['#dcfce7','#16a34a','🟢'], 'draft' => ['#f3f4f6','#6b7280','⚪'], 'closed' => ['#fee2e2','#dc2626','🔴']];
                    [$sbg,$stc,$dot] = $sc[$event->status] ?? ['#f3f4f6','#6b7280','⚪'];
                @endphp
                <div x-show="tab === 'all' || tab === '{{ $event->status }}'"
                     style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f9fafb;transition:background .12s"
                     onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">

                    {{-- Flyer / Icon --}}
                    @if($event->flyer_path)
                    <img src="{{ asset('storage/'.$event->flyer_path) }}" style="width:48px;height:48px;border-radius:12px;object-fit:cover;flex-shrink:0;border:2px solid #f3f4f6">
                    @else
                    <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#f3f0ff,#fce7f3);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:22px;height:22px;color:#7c3aed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif

                    {{-- Info --}}
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px">
                            <p style="font-weight:700;color:#1a0030;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $event->name }}</p>
                            <span style="background:{{ $sbg }};color:{{ $stc }};padding:2px 9px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;flex-shrink:0">{{ $event->status }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px">
                            <span style="font-size:11.5px;color:#9ca3af;display:flex;align-items:center;gap:4px">
                                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $event->ends_at?->format('d M Y') ?? '—' }}
                            </span>
                            <span style="font-size:11.5px;color:#9ca3af;display:flex;align-items:center;gap:4px">
                                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $cats }} categor{{ $cats == 1 ? 'y' : 'ies' }}
                            </span>
                            <span style="font-size:11.5px;color:#9ca3af;display:flex;align-items:center;gap:4px">
                                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $noms }} nominees
                            </span>
                        </div>
                    </div>

                    {{-- Revenue + Votes --}}
                    <div style="text-align:right;flex-shrink:0;min-width:100px">
                        <p style="font-weight:800;color:#059669;font-size:13.5px">GHS {{ number_format($rev/100,2) }}</p>
                        <p style="color:#9ca3af;font-size:11.5px;margin-top:1px">{{ number_format($event->votes_count) }} votes</p>
                    </div>

                    {{-- Manage --}}
                    <a href="{{ route('admin.events.show', $event) }}"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:white;background:#1a0030;padding:7px 13px;border-radius:9px;text-decoration:none;flex-shrink:0;white-space:nowrap"
                       onmouseover="this.style.background='#e91e8c'" onmouseout="this.style.background='#1a0030'">
                        Manage
                        <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>

                </div>
                @endforeach
                @endif

                @if($events->count() > 8)
                <div style="padding:14px 20px;text-align:center;border-top:1px solid #f3f4f6">
                    <a href="{{ route('admin.events.index') }}"
                       style="font-size:13px;font-weight:700;color:#7c3aed;text-decoration:none">
                        View all {{ $events->count() }} events →
                    </a>
                </div>
                @endif

            </div>
        </div>

        {{-- ── Right sidebar ── --}}
        <div style="display:flex;flex-direction:column;gap:18px">

            {{-- Quick Actions --}}
            <div style="background:white;border-radius:18px;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:18px">
                <h3 style="font-size:14px;font-weight:800;color:#1a0030;margin-bottom:14px">Quick Actions</h3>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @foreach([
                        [route('admin.events.create'),  'M12 4v16m8-8H4',                                                                      'linear-gradient(135deg,#e91e8c,#ad1070)', 'Create New Event',    'Launch a voting event'],
                        [route('admin.events.index'),   'M4 6h16M4 10h16M4 14h16M4 18h16',                                                     'linear-gradient(135deg,#4f46e5,#7c3aed)', 'Manage Events',       'View & edit your events'],
                        [route('admin.transactions'),   'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','linear-gradient(135deg,#f59e0b,#d97706)', 'Transactions',       'View payment history'],
                        [route('admin.vote-results'),   'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','linear-gradient(135deg,#059669,#10b981)','Vote Results',    'See live results'],
                        [route('admin.profile'),        'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',                 'linear-gradient(135deg,#6b7280,#4b5563)', 'Account Settings',   'Profile & security'],
                    ] as [$href, $path, $grad, $title, $desc])
                    <a href="{{ $href }}"
                       style="display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:12px;border:1px solid #f3f4f6;text-decoration:none;transition:all .15s"
                       onmouseover="this.style.borderColor='#e5e7eb';this.style.background='#fafafa'" onmouseout="this.style.borderColor='#f3f4f6';this.style.background=''">
                        <div style="width:36px;height:36px;border-radius:10px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 8px rgba(0,0,0,.15)">
                            <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:700;color:#1a0030;margin-bottom:1px">{{ $title }}</p>
                            <p style="font-size:11.5px;color:#9ca3af">{{ $desc }}</p>
                        </div>
                        <svg style="width:14px;height:14px;color:#d1d5db;margin-left:auto;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div style="background:white;border-radius:18px;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:18px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                    <h3 style="font-size:14px;font-weight:800;color:#1a0030">Recent Payments</h3>
                    <a href="{{ route('admin.transactions') }}" style="font-size:12px;font-weight:700;color:#e91e8c;text-decoration:none">See all →</a>
                </div>

                @if($recentPayments->isEmpty())
                <div style="text-align:center;padding:24px 0">
                    <svg style="width:36px;height:36px;color:#e5e7eb;margin:0 auto 8px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p style="font-size:12.5px;color:#9ca3af">No payments yet</p>
                </div>
                @else
                <div style="display:flex;flex-direction:column;gap:2px">
                    @foreach($recentPayments as $pmt)
                    @php
                        $psc = ['success' => ['#dcfce7','#16a34a'], 'pending' => ['#fef3c7','#d97706'], 'failed' => ['#fee2e2','#dc2626']];
                        [$pbg,$ptc] = $psc[$pmt->status] ?? ['#f3f4f6','#6b7280'];
                    @endphp
                    <div style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;transition:background .12s"
                         onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                        <div style="width:34px;height:34px;border-radius:10px;background:{{ $pbg }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg style="width:15px;height:15px;color:{{ $ptc }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                @if($pmt->status === 'success')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                @elseif($pmt->status === 'pending')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                @endif
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <p style="font-size:12.5px;font-weight:700;color:#1a0030;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ Str::limit($pmt->event?->name ?? 'Unknown Event', 22) }}</p>
                            <p style="font-size:11px;color:#9ca3af">{{ $pmt->created_at->diffForHumans() }}</p>
                        </div>
                        <div style="text-align:right;flex-shrink:0">
                            <p style="font-size:12.5px;font-weight:800;color:#059669">GHS {{ number_format($pmt->amount_pesewas/100,2) }}</p>
                            <span style="background:{{ $pbg }};color:{{ $ptc }};font-size:9.5px;font-weight:700;padding:1px 7px;border-radius:20px;text-transform:uppercase">{{ $pmt->status }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Pending alert --}}
            @if($pendingCount > 0)
            <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fde68a;border-radius:14px;padding:16px">
                <div style="display:flex;align-items:center;gap:9px;margin-bottom:6px">
                    <div style="width:34px;height:34px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:17px;height:17px;color:#d97706" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight:700;color:#78350f;font-size:13px">{{ $pendingCount }} Pending Payment{{ $pendingCount > 1 ? 's' : '' }}</p>
                        <p style="font-size:11.5px;color:#92400e">Awaiting reconciliation</p>
                    </div>
                </div>
                <a href="{{ route('admin.transactions') }}"
                   style="display:block;text-align:center;background:#d97706;color:white;font-size:12.5px;font-weight:700;padding:8px;border-radius:9px;text-decoration:none;margin-top:8px">
                    Review Payments
                </a>
            </div>
            @endif

        </div>{{-- end right sidebar --}}
    </div>{{-- end 2-col grid --}}

</div>
