<div class="space-y-8">

    {{-- ── Welcome bar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Welcome back, {{ Str::words(auth('admin')->user()->name, 1, '') }} 👋
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Here's what's happening across your events today — {{ now()->format('l, d F Y') }}
            </p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-2.5 rounded-xl transition shadow-sm text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Live Events --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Live Events</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2 leading-none">{{ $liveEvents }}</p>
                    <p class="text-xs text-slate-400 mt-1.5">
                        @if($liveEvents > 0)
                            <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse inline-block"></span>
                                Accepting votes now
                            </span>
                        @else
                            No active events
                        @endif
                    </p>
                </div>
                <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M8.464 15.536a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500 rounded-b-2xl opacity-60"></div>
        </div>

        {{-- Total Votes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Votes</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2 leading-none">{{ number_format($totalVotes) }}</p>
                    <p class="text-xs text-slate-400 mt-1.5">Across all events</p>
                </div>
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-500 rounded-b-2xl opacity-60"></div>
        </div>

        {{-- Revenue --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Revenue (GHS)</p>
                    <p class="text-4xl font-extrabold text-slate-800 mt-2 leading-none">
                        {{ number_format($totalRevenue / 100, 0) }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1.5">Confirmed payments</p>
                </div>
                <div class="w-11 h-11 bg-brand-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-brand-500 rounded-b-2xl opacity-60"></div>
        </div>

        {{-- Pending Payments --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending</p>
                    <p class="text-4xl font-extrabold {{ $pendingCount > 0 ? 'text-amber-500' : 'text-slate-800' }} mt-2 leading-none">
                        {{ $pendingCount }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1.5">
                        @if($pendingCount > 0)
                            <span class="text-amber-500 font-medium">Awaiting confirmation</span>
                        @else
                            All payments settled
                        @endif
                    </p>
                </div>
                <div class="w-11 h-11 {{ $pendingCount > 0 ? 'bg-amber-50' : 'bg-slate-50' }} rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $pendingCount > 0 ? 'text-amber-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 {{ $pendingCount > 0 ? 'bg-amber-400' : 'bg-slate-200' }} rounded-b-2xl opacity-60"></div>
        </div>
    </div>

    {{-- ── Body: Events table + Quick actions ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Events table (2/3 width on xl) --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-slate-800 text-sm">Your Events</h3>
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-medium">{{ $events->count() }}</span>
                </div>
                <a href="{{ route('admin.events.index') }}" class="text-xs text-brand-600 hover:text-brand-700 font-semibold">
                    View all →
                </a>
            </div>

            <div class="divide-y divide-slate-50">
                @forelse($events->take(8) as $event)
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition group">

                    {{-- Flyer thumbnail or placeholder --}}
                    <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-slate-100 flex items-center justify-center">
                        @if($event->flyer_path)
                            <img src="{{ asset('storage/'.$event->flyer_path) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Name + type --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $event->name }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs px-1.5 py-0.5 rounded font-medium
                                {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : ($event->event_type === 'election' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                                {{ strtoupper($event->event_type) }}
                            </span>
                            <span class="text-xs text-slate-400">
                                {{ $event->ends_at?->format('d M Y') ?? 'No end date' }}
                            </span>
                        </div>
                    </div>

                    {{-- Vote count --}}
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-slate-700 font-mono">{{ number_format($event->votes_count) }}</p>
                        <p class="text-xs text-slate-400">votes</p>
                    </div>

                    {{-- Status badge --}}
                    <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $event->status === 'live' ? 'bg-emerald-100 text-emerald-700' : ($event->status === 'draft' ? 'bg-slate-100 text-slate-600' : 'bg-red-100 text-red-600') }}">
                        @if($event->status === 'live')
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                LIVE
                            </span>
                        @else
                            {{ strtoupper($event->status) }}
                        @endif
                    </span>

                    {{-- Actions (visible on hover) --}}
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition shrink-0">
                        <a href="{{ route('admin.events.results', $event) }}"
                           class="text-xs text-brand-600 hover:text-brand-700 font-medium whitespace-nowrap">Results</a>
                        <a href="{{ route('admin.events.edit', $event) }}"
                           class="text-xs text-slate-400 hover:text-slate-600 whitespace-nowrap">Edit</a>
                    </div>
                </div>
                @empty
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-slate-600 font-semibold text-sm">No events yet</p>
                    <p class="text-slate-400 text-xs mt-1">Create your first voting event to get started</p>
                    <a href="{{ route('admin.events.create') }}"
                       class="inline-block mt-4 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                        + Create Event
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Quick actions sidebar (1/3 width on xl) --}}
        <div class="space-y-4">

            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.events.create') }}"
                       class="flex items-center gap-3 p-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white transition text-sm font-medium shadow-sm">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New Event
                    </a>
                    <a href="{{ route('admin.events.index') }}"
                       class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Manage Events
                    </a>
                    <a href="{{ route('vote.index') }}" target="_blank"
                       class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition text-sm font-medium">
                        <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Preview Voting Site
                    </a>
                </div>
            </div>

            {{-- Live Events spotlight --}}
            @if($events->where('status','live')->count() > 0)
            <div class="bg-emerald-950 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <h3 class="font-semibold text-emerald-300 text-sm uppercase tracking-wide">Live Now</h3>
                </div>
                <div class="space-y-3">
                    @foreach($events->where('status','live') as $live)
                    <div class="bg-emerald-900/50 rounded-xl p-3">
                        <p class="text-white text-sm font-semibold truncate">{{ $live->name }}</p>
                        <div class="flex items-center justify-between mt-1.5">
                            <p class="text-emerald-400 text-xs font-mono font-bold">
                                {{ number_format($live->votes_count) }} votes
                            </p>
                            <a href="{{ route('admin.events.results', $live) }}"
                               class="text-xs text-emerald-300 hover:text-white font-medium">Results →</a>
                        </div>
                        @if($live->ends_at)
                        <p class="text-emerald-600 text-xs mt-1">Closes {{ $live->ends_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-slate-800 rounded-2xl p-5 text-center">
                <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M8.464 15.536a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072"/>
                    </svg>
                </div>
                <p class="text-slate-400 text-xs">No live events right now</p>
            </div>
            @endif

            {{-- USSD info card --}}
            <div class="bg-navy-900 rounded-2xl p-5">
                <h3 class="font-semibold text-white text-sm mb-2">USSD Voting</h3>
                <p class="text-slate-400 text-xs leading-relaxed mb-3">
                    Voters without internet can dial from any Ghana network.
                </p>
                <div class="bg-slate-800 rounded-xl px-4 py-3 text-center">
                    <p class="text-brand-400 font-bold text-2xl tracking-widest font-mono">*928#</p>
                    <p class="text-slate-500 text-xs mt-1">MTN · Vodafone · AirtelTigo</p>
                </div>
            </div>

        </div>
    </div>

</div>
