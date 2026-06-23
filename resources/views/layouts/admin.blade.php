<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — CastVote Organizer Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar nav links ── */
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            color: rgba(255,255,255,.65);
            transition: all .15s; text-decoration: none;
            white-space: nowrap; width: 100%; border: none;
            background: transparent; cursor: pointer; text-align: left;
        }
        .nav-link:hover  { background: rgba(255,255,255,.08); color: #fff; }
        .nav-link.active { background: #e91e8c; color: #fff; box-shadow: 0 4px 14px rgba(233,30,140,.4); }
        .nav-link .icon  { width: 17px; height: 17px; flex-shrink: 0; }

        /* section labels */
        .nav-section {
            font-size: 10.5px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,.35);
            padding: 0 14px; margin-top: 22px; margin-bottom: 4px;
        }

        /* sub-links */
        .sub-link {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 14px 8px 40px; border-radius: 8px;
            font-size: 13px; font-weight: 500; color: rgba(255,255,255,.5);
            transition: all .15s; text-decoration: none; white-space: nowrap;
        }
        .sub-link:hover  { background: rgba(255,255,255,.06); color: rgba(255,255,255,.85); }
        .sub-link.active { color: #e91e8c; background: rgba(233,30,140,.12); }

        @keyframes softpulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        .live-pulse { animation: softpulse 2s infinite; }
    </style>
</head>
<body style="background:#f0eff4;min-height:100vh"
      x-data="{
          sidebarOpen: false,
          eventsOpen: {{ request()->routeIs('admin.events.*') ? 'true' : 'false' }},
      }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
     style="position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:30;backdrop-filter:blur(4px)"
     class="lg:hidden"></div>

{{-- ══════════════════════════════════════
     SIDEBAR
══════════════════════════════════════ --}}
<aside style="position:fixed;top:0;left:0;bottom:0;width:168px;z-index:40;display:flex;flex-direction:column;background:linear-gradient(180deg,#2d0050 0%,#1a0030 100%)"
       class="transition-transform duration-300 -translate-x-full lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : ''">

    {{-- Brand --}}
    <div style="padding:18px 16px 16px;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:10px">
        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#e91e8c,#ad1070);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(233,30,140,.45)">
            <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div style="min-width:0">
            <p style="color:white;font-weight:800;font-size:14px;line-height:1.2;letter-spacing:-.2px">CastVote</p>
            <p style="color:rgba(255,255,255,.4);font-size:10px;font-weight:500">Organizer Portal</p>
        </div>
        <button @click="sidebarOpen=false"
                style="margin-left:auto;color:rgba(255,255,255,.4);flex-shrink:0;background:none;border:none;cursor:pointer"
                class="lg:hidden">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav style="flex:1;padding:8px 8px;overflow-y:auto">

        <div class="nav-section" style="margin-top:8px">Menu</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- MY EVENTS --}}
        <div class="nav-section">My Events</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link {{ request()->routeIs('admin.events.index') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Events List
            @php $liveCount = \App\Models\Event::where('organization_id', auth('admin')->user()?->organization_id)->where('status','live')->count(); @endphp
            @if($liveCount)
            <span style="margin-left:auto;background:rgba(16,185,129,.2);color:#34d399;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px" class="live-pulse">{{ $liveCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.events.create') }}"
           class="nav-link {{ request()->routeIs('admin.events.create') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Create Event
        </a>

        {{-- VOTING --}}
        <div class="nav-section">Voting</div>

        <a href="{{ route('admin.nominations') }}"
           class="nav-link {{ request()->routeIs('admin.nominations') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Nominations
        </a>

        <a href="{{ route('admin.vote-results') }}"
           class="nav-link {{ request()->routeIs('admin.vote-results') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Vote Results
        </a>

        {{-- FINANCIAL --}}
        <div class="nav-section">Financial</div>

        <a href="{{ route('admin.transactions') }}"
           class="nav-link {{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Transactions
            @php $pending = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
            @if($pending)
            <span style="margin-left:auto;background:rgba(245,158,11,.2);color:#fbbf24;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px">{{ $pending }}</span>
            @endif
        </a>

        <a href="{{ route('admin.earnings') }}"
           class="nav-link {{ request()->routeIs('admin.earnings') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Earnings
        </a>

        {{-- SETTINGS --}}
        <div class="nav-section">Settings</div>

        <a href="{{ route('admin.profile') }}"
           class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profile
        </a>

        {{-- SECURITY --}}
        <div class="nav-section">Security</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Fraud Panels
            <span style="margin-left:auto;font-size:10px;color:rgba(255,255,255,.3)">per event</span>
        </a>

        <a href="{{ route('admin.audit') }}"
           class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Audit Log
        </a>

        {{-- PLATFORM (superadmin only) --}}
        @if(auth('admin')->user()?->isSuperAdmin())
        @php $pendingCount = \App\Models\Admin::where('is_superadmin', false)->where('account_status', 'pending')->count(); @endphp
        <div class="nav-section">Platform</div>

        <a href="{{ route('admin.approvals') }}"
           class="nav-link {{ request()->routeIs('admin.approvals') ? 'active' : '' }}">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Approvals
            @if($pendingCount > 0)
            <span style="margin-left:auto;background:#e91e8c;color:white;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px" class="live-pulse">{{ $pendingCount }}</span>
            @endif
        </a>
        @endif

    </nav>

    {{-- Sign out --}}
    @auth('admin')
    <div style="padding:12px 8px;border-top:1px solid rgba(255,255,255,.07)">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="color:rgba(255,100,100,.7)">
                <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
    @endauth
</aside>

{{-- ══════════════════════════════════════
     MAIN CONTENT AREA
══════════════════════════════════════ --}}
<div style="padding-left:0" class="lg:pl-[168px] min-h-screen flex flex-col">

    {{-- Topbar --}}
    <header style="background:white;border-bottom:1px solid #e5e7eb;position:sticky;top:0;z-index:20;box-shadow:0 1px 3px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;gap:12px;padding:0 20px;height:56px">

            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen=true"
                    style="background:none;border:none;cursor:pointer;color:#6b7280;padding:6px"
                    class="lg:hidden">
                <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Search --}}
            <div style="flex:1;max-width:300px;position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search..."
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:7px 12px 7px 32px;font-size:13px;background:#f9fafb;color:#374151;outline:none">
            </div>

            <div style="flex:1"></div>

            {{-- Bell --}}
            @php $bell = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
            <div style="position:relative">
                <div style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;display:flex;align-items:center;justify-content:center;cursor:pointer;background:white">
                    <svg style="width:17px;height:17px;color:#6b7280" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                @if($bell > 0)
                <span style="position:absolute;top:-4px;right:-4px;width:17px;height:17px;background:#e91e8c;color:white;font-size:9px;font-weight:800;border-radius:50%;display:flex;align-items:center;justify-content:center">{{ $bell > 9 ? '9+' : $bell }}</span>
                @endif
            </div>

            {{-- User info --}}
            @auth('admin')
            <div style="display:flex;align-items:center;gap:10px;padding:6px 10px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer">
                <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#e91e8c,#ad1070);display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
                </div>
                <div style="min-width:0">
                    <p style="font-size:12.5px;font-weight:600;color:#1a0030;line-height:1.2;white-space:nowrap">{{ Str::words(auth('admin')->user()->name, 1, '') }}</p>
                    <p style="font-size:11px;color:#9ca3af;line-height:1.2">{{ ucfirst(auth('admin')->user()->role) }}</p>
                </div>
                <svg style="width:14px;height:14px;color:#9ca3af;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            @endauth
        </div>
    </header>

    {{-- Pending approval banner --}}
    @auth('admin')
    @if(auth('admin')->user()->isPending())
    <div style="background:#fffbeb;border-bottom:1px solid #fde68a;padding:12px 24px;display:flex;align-items:center;gap:12px">
        <svg style="width:18px;height:18px;color:#d97706;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p style="font-size:13.5px;color:#78350f;flex:1">
            Your organizer account is currently <strong>pending admin approval</strong>. You can create event drafts, but you won't be able to submit or publish them until approved.
        </p>
        <span style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;white-space:nowrap;flex-shrink:0;letter-spacing:.04em">
            ⏳ PENDING REVIEW
        </span>
    </div>
    @endif
    @endauth

    {{-- Flash messages --}}
    @if(session('success'))
    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4500)" x-cloak
         style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;padding:12px 24px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#166534">
        <svg style="width:16px;height:16px;color:#22c55e;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border-bottom:1px solid #fecaca;padding:12px 24px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#dc2626">
        <svg style="width:16px;height:16px;color:#ef4444;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Page content --}}
    <main style="flex:1;padding:28px 24px">
        {{ $slot }}
    </main>
</div>

@livewireScripts
</body>
</html>
