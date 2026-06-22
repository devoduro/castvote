<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — CastVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#fff7ed',100:'#ffedd5',200:'#fed7aa',300:'#fdba74',400:'#fb923c',500:'#f97316',600:'#ea580c',700:'#c2410c',800:'#9a3412',900:'#7c2d12' },
                        navy:  { 950:'#060d1a',900:'#0d1526',850:'#111d33',800:'#162034',750:'#1a273d',700:'#223047',600:'#2e4060' },
                    },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        [x-cloak]  { display:none !important; }

        /* sidebar nav links */
        .nav-link {
            display:flex; align-items:center; gap:10px;
            padding:9px 12px; border-radius:8px;
            font-size:.8125rem; font-weight:500;
            color:#94a3b8; transition:all .15s;
            text-decoration:none; white-space:nowrap;
        }
        .nav-link:hover  { background:rgba(255,255,255,.07); color:#f1f5f9; }
        .nav-link.active { background:linear-gradient(135deg,#ea580c,#f97316); color:#fff;
                           box-shadow:0 4px 14px rgba(234,88,12,.35); }
        .nav-link .icon  { width:17px; height:17px; flex-shrink:0; }

        /* section labels */
        .nav-section { font-size:.65rem; font-weight:700; letter-spacing:.1em;
                       text-transform:uppercase; color:#334155; padding:0 12px;
                       margin-top:20px; margin-bottom:4px; }

        /* sub-link inside collapse */
        .sub-link { display:flex; align-items:center; gap:8px;
                    padding:7px 12px 7px 36px; border-radius:8px;
                    font-size:.78rem; font-weight:500; color:#64748b;
                    transition:all .15s; text-decoration:none; }
        .sub-link:hover  { background:rgba(255,255,255,.05); color:#e2e8f0; }
        .sub-link.active { color:#fb923c; background:rgba(251,146,60,.08); }

        /* pulse for live badge */
        @keyframes pulse2 { 0%,100%{opacity:1} 50%{opacity:.4} }
        .live-pulse { animation:pulse2 1.8s infinite; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased"
      x-data="{
          sidebarOpen: false,
          eventsOpen: {{ request()->routeIs('admin.events.*') ? 'true' : 'false' }},
          reportsOpen: false,
      }">

{{-- ── Mobile overlay ── --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/60 z-30 lg:hidden backdrop-blur-sm"></div>

{{-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ --}}
<aside class="fixed inset-y-0 left-0 w-[252px] z-40 flex flex-col
              transition-transform duration-300 -translate-x-full lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : ''"
       style="background:linear-gradient(180deg,#0d1526 0%,#0a1020 100%);">

    {{-- ── Logo / Brand ── --}}
    <div class="flex items-center gap-3 px-5 py-[18px] border-b border-white/5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
             style="background:linear-gradient(135deg,#ea580c,#f97316);box-shadow:0 4px 12px rgba(234,88,12,.45)">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-white font-bold text-[15px] leading-tight tracking-tight">CastVote</p>
            <p class="text-slate-500 text-[11px] font-medium">Admin Portal</p>
        </div>
        {{-- Close btn (mobile) --}}
        <button @click="sidebarOpen=false" class="ml-auto lg:hidden text-slate-600 hover:text-slate-400 shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── Admin profile card ── --}}
    @auth('admin')
    <div class="mx-3 mt-3 mb-1 rounded-xl px-3 py-3 flex items-center gap-3"
         style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06)">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold shrink-0"
             style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e)">
            {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-slate-200 text-xs font-semibold truncate leading-tight">{{ auth('admin')->user()->name }}</p>
            <p class="text-slate-500 text-[11px] truncate">{{ auth('admin')->user()->email }}</p>
        </div>
        <span class="shrink-0 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide
            {{ auth('admin')->user()->role === 'owner' ? 'bg-brand-900/60 text-brand-400' :
               (auth('admin')->user()->role === 'manager' ? 'bg-blue-900/60 text-blue-400' : 'bg-slate-700 text-slate-400') }}">
            {{ auth('admin')->user()->role }}
        </span>
    </div>
    @endauth

    {{-- ── Navigation ── --}}
    <nav class="flex-1 px-3 py-2 overflow-y-auto space-y-0.5 scrollbar-thin">

        {{-- ─ OVERVIEW ─ --}}
        <div class="nav-section">Overview</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- ─ EVENTS (collapsible) ─ --}}
        <div class="nav-section">Events</div>

        {{-- Events parent toggle --}}
        <button @click="eventsOpen = !eventsOpen"
                class="nav-link w-full text-left {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="flex-1">All Events</span>
            {{-- live badge --}}
            @php $liveCount = \App\Models\Event::where('organization_id', auth('admin')->user()?->organization_id)->where('status','live')->count(); @endphp
            @if($liveCount)
            <span class="flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-900/60 text-emerald-400 live-pulse">
                <span class="w-1 h-1 bg-emerald-400 rounded-full inline-block"></span>{{ $liveCount }}
            </span>
            @endif
            <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200" :class="eventsOpen ? 'rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="eventsOpen" x-cloak x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
             class="space-y-0.5">
            <a href="{{ route('admin.events.index') }}"
               class="sub-link {{ request()->routeIs('admin.events.index') ? 'active' : '' }}">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Manage Events
            </a>
            <a href="{{ route('admin.events.create') }}"
               class="sub-link {{ request()->routeIs('admin.events.create') ? 'active' : '' }}">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Create Event
            </a>
        </div>

        {{-- ─ VOTING ─ --}}
        <div class="nav-section">Voting</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Nominees
        </a>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Eligible Voters
        </a>

        {{-- ─ FINANCIALS ─ --}}
        <div class="nav-section">Financials</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span class="flex-1">Payments</span>
            @php $pending = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
            @if($pending)
            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-900/60 text-amber-400">{{ $pending }}</span>
            @endif
        </a>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Results & Reports
        </a>

        {{-- ─ SECURITY ─ --}}
        <div class="nav-section">Security</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Fraud Panel
        </a>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Vote Integrity
        </a>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Audit Log
        </a>

        {{-- ─ PLATFORM (superadmin only) ─ --}}
        @if(auth('admin')->user()?->isSuperAdmin())
        @php $pendingCount = \App\Models\Admin::where('is_superadmin', false)->where('account_status', 'pending')->count(); @endphp
        <div class="nav-section">Platform</div>

        <a href="{{ route('admin.approvals') }}"
           class="nav-link {{ request()->routeIs('admin.approvals') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Account Approvals
            @if($pendingCount > 0)
            <span style="margin-left:auto;background:#f59e0b;color:#78350f;font-size:10px;font-weight:800;padding:1px 6px;border-radius:20px;animation:pulse 2s infinite">
                {{ $pendingCount }}
            </span>
            @endif
        </a>
        @endif

        {{-- ─ SYSTEM ─ --}}
        <div class="nav-section">System</div>

        <a href="{{ route('vote.index') }}" target="_blank" class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Public Voting Site
        </a>

        <a href="{{ route('vote.privacy') }}" class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Privacy Policy
        </a>

    </nav>

    {{-- ── Bottom: Sign out ── --}}
    @auth('admin')
    <div class="px-3 py-3 border-t border-white/5">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                    class="nav-link w-full text-left text-red-400/80 hover:text-red-300 hover:bg-red-950/40">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>

        <div class="mt-2 px-3 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 live-pulse shrink-0"></span>
            <p class="text-[11px] text-slate-600 truncate">v1.0 · CastVote Ghana</p>
        </div>
    </div>
    @endauth
</aside>

{{-- ════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ --}}
<div class="lg:pl-[252px] min-h-screen flex flex-col">

    {{-- ── Top bar ── --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20"
            style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div class="flex items-center gap-3 px-4 sm:px-6 h-14">

            {{-- Mobile menu toggle --}}
            <button @click="sidebarOpen=true"
                    class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title / breadcrumb --}}
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <span class="text-slate-400 text-xs hidden sm:block">Admin</span>
                <span class="text-slate-300 text-xs hidden sm:block">/</span>
                <span class="text-slate-800 font-semibold text-sm truncate">{{ $title ?? 'Dashboard' }}</span>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- New Event shortcut --}}
                <a href="{{ route('admin.events.create') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg text-white transition"
                   style="background:linear-gradient(135deg,#ea580c,#f97316);box-shadow:0 2px 8px rgba(234,88,12,.3)">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Event
                </a>

                {{-- Pending payments bell --}}
                @php $bell = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
                <div class="relative">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 transition cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    @if($bell > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                        {{ $bell > 9 ? '9+' : $bell }}
                    </span>
                    @endif
                </div>

                {{-- Admin avatar (topbar) --}}
                @auth('admin')
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0 cursor-pointer"
                     style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e)">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
                </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
    <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false,4500)" x-cloak
         class="flex items-center gap-3 px-6 py-3 text-sm font-medium bg-emerald-50 border-b border-emerald-200 text-emerald-800">
        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-6 py-3 text-sm font-medium bg-red-50 border-b border-red-200 text-red-700">
        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
</div>

@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
