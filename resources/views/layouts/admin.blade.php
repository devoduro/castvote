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

        :root {
            --sidebar-w: 240px;
            --sidebar-collapsed: 64px;
            --pink: #e91e8c;
            --purple-dark: #1a0030;
            --purple: #2d0050;
        }

        /* ── Sidebar transition ── */
        #sidebar {
            transition: width .25s cubic-bezier(.4,0,.2,1);
        }
        #main-content {
            transition: padding-left .25s cubic-bezier(.4,0,.2,1);
        }

        /* ── Nav links ── */
        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,.58);
            transition: all .15s; text-decoration: none;
            white-space: nowrap; width: 100%; border: none;
            background: transparent; cursor: pointer; text-align: left;
            position: relative;
        }
        .nav-link:hover { background: rgba(255,255,255,.08); color: rgba(255,255,255,.9); }
        .nav-link.active {
            background: linear-gradient(135deg,#e91e8c,#7c3aed);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(233,30,140,.35);
        }
        .nav-link .icon { width: 17px; height: 17px; flex-shrink: 0; }

        /* ── Section labels ── */
        .nav-section {
            font-size: 10px; font-weight: 700; letter-spacing: .11em;
            text-transform: uppercase; color: rgba(255,255,255,.25);
            padding: 0 12px; margin-top: 18px; margin-bottom: 2px;
            white-space: nowrap; overflow: hidden;
        }

        /* ── Sub-links ── */
        .sub-link {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 12px 8px 42px; border-radius: 10px;
            font-size: 13px; font-weight: 500; color: rgba(255,255,255,.45);
            transition: all .15s; text-decoration: none; white-space: nowrap;
        }
        .sub-link:hover  { background: rgba(255,255,255,.06); color: rgba(255,255,255,.8); }
        .sub-link.active { color: #f472b6; background: rgba(233,30,140,.1); }

        /* ── Badge ── */
        .nav-badge {
            margin-left: auto; font-size: 10px; font-weight: 700;
            padding: 1px 7px; border-radius: 20px; flex-shrink: 0;
            line-height: 1.6; white-space: nowrap;
        }

        /* ── Tooltip (shown only when collapsed) ── */
        .nav-tooltip {
            position: absolute; left: calc(var(--sidebar-collapsed) - 8px);
            background: #1e1e2e; color: white; font-size: 12px; font-weight: 600;
            padding: 6px 12px; border-radius: 8px; white-space: nowrap;
            pointer-events: none; opacity: 0; transition: opacity .15s;
            box-shadow: 0 4px 16px rgba(0,0,0,.35); z-index: 100;
            border: 1px solid rgba(255,255,255,.08);
        }
        .nav-link:hover .nav-tooltip { opacity: 1; }

        /* ── Scrollbar ── */
        nav::-webkit-scrollbar { width: 3px; }
        nav::-webkit-scrollbar-track { background: transparent; }
        nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 3px; }

        @keyframes softpulse { 0%,100%{opacity:1} 50%{opacity:.5} }
        .live-pulse { animation: softpulse 2s infinite; }
    </style>
</head>
<body style="background:#f0eff4;min-height:100vh"
      x-data="{
          sidebarOpen: false,
          collapsed: JSON.parse(localStorage.getItem('cv_sidebar_collapsed') || 'false'),
          eventsOpen: {{ request()->routeIs('admin.events.*') ? 'true' : 'false' }},
          toggleCollapse() {
              this.collapsed = !this.collapsed;
              localStorage.setItem('cv_sidebar_collapsed', this.collapsed);
          }
      }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
     style="position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:30;backdrop-filter:blur(3px)"
     class="lg:hidden"></div>

{{-- ══════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════ --}}
<aside id="sidebar"
       :style="collapsed ? 'width:var(--sidebar-collapsed)' : 'width:var(--sidebar-w)'"
       style="position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);z-index:40;display:flex;flex-direction:column;background:linear-gradient(180deg,#2d0050 0%,#1a0030 100%);overflow:hidden"
       class="transition-transform duration-300 -translate-x-full lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : ''">

    {{-- ── Brand ── --}}
    <div style="padding:16px 14px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:10px;flex-shrink:0;min-height:64px">
        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#e91e8c,#ad1070);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(233,30,140,.45)">
            <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div style="min-width:0;overflow:hidden" x-show="!collapsed" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <p style="color:white;font-weight:800;font-size:14.5px;line-height:1.2;letter-spacing:-.3px;white-space:nowrap">CastVote</p>
            <p style="color:rgba(255,255,255,.38);font-size:10px;font-weight:500;white-space:nowrap">Organizer Portal</p>
        </div>
        {{-- Mobile close --}}
        <button @click="sidebarOpen=false"
                style="margin-left:auto;color:rgba(255,255,255,.4);flex-shrink:0;background:none;border:none;cursor:pointer;padding:4px"
                class="lg:hidden" x-show="!collapsed">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        {{-- Desktop collapse toggle --}}
        <button @click="toggleCollapse()"
                :style="collapsed ? 'margin-left:0' : 'margin-left:auto'"
                style="flex-shrink:0;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);cursor:pointer;padding:5px;border-radius:8px;color:rgba(255,255,255,.5);display:flex;align-items:center;justify-content:center;transition:all .15s"
                onmouseover="this.style.background='rgba(255,255,255,.13)';this.style.color='white'"
                onmouseout="this.style.background='rgba(255,255,255,.07)';this.style.color='rgba(255,255,255,.5)'"
                class="hidden lg:flex">
            <svg style="width:15px;height:15px;transition:transform .25s" :style="collapsed ? 'transform:rotate(180deg)' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    {{-- ── Nav ── --}}
    <nav style="flex:1;padding:8px 10px;overflow-y:auto;overflow-x:hidden">

        {{-- MENU --}}
        <div class="nav-section" x-show="!collapsed">Menu</div>
        <div style="margin-top:8px" x-show="collapsed"></div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Dashboard</span>
            <span class="nav-tooltip" x-show="collapsed">Dashboard</span>
        </a>

        {{-- MY EVENTS --}}
        <div class="nav-section" x-show="!collapsed">My Events</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link {{ request()->routeIs('admin.events.index') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Events List</span>
            @php $liveCount = \App\Models\Event::where('organization_id', auth('admin')->user()?->organization_id)->where('status','live')->count(); @endphp
            @if($liveCount)
            <span class="nav-badge live-pulse" style="background:rgba(16,185,129,.2);color:#34d399" x-show="!collapsed">{{ $liveCount }}</span>
            @endif
            <span class="nav-tooltip" x-show="collapsed">Events List @if($liveCount)({{ $liveCount }} live)@endif</span>
        </a>

        <a href="{{ route('admin.events.create') }}"
           class="nav-link {{ request()->routeIs('admin.events.create') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Create Event</span>
            <span class="nav-tooltip" x-show="collapsed">Create Event</span>
        </a>

        {{-- VOTING --}}
        <div class="nav-section" x-show="!collapsed">Voting</div>

        <a href="{{ route('admin.nominations') }}"
           class="nav-link {{ request()->routeIs('admin.nominations') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Nominations</span>
            <span class="nav-tooltip" x-show="collapsed">Nominations</span>
        </a>

        <a href="{{ route('admin.vote-results') }}"
           class="nav-link {{ request()->routeIs('admin.vote-results') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Vote Results</span>
            <span class="nav-tooltip" x-show="collapsed">Vote Results</span>
        </a>

        {{-- FINANCIAL --}}
        <div class="nav-section" x-show="!collapsed">Financial</div>

        <a href="{{ route('admin.transactions') }}"
           class="nav-link {{ request()->routeIs('admin.transactions') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Transactions</span>
            @php $pending = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
            @if($pending)
            <span class="nav-badge" style="background:rgba(245,158,11,.2);color:#fbbf24" x-show="!collapsed">{{ $pending }}</span>
            @endif
            <span class="nav-tooltip" x-show="collapsed">Transactions @if($pending)({{ $pending }} pending)@endif</span>
        </a>

        <a href="{{ route('admin.earnings') }}"
           class="nav-link {{ request()->routeIs('admin.earnings') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Earnings</span>
            <span class="nav-tooltip" x-show="collapsed">Earnings</span>
        </a>

        {{-- SECURITY --}}
        <div class="nav-section" x-show="!collapsed">Security</div>

        <a href="{{ route('admin.audit') }}"
           class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Audit Log</span>
            <span class="nav-tooltip" x-show="collapsed">Audit Log</span>
        </a>

        {{-- SETTINGS --}}
        <div class="nav-section" x-show="!collapsed">Settings</div>

        <a href="{{ route('admin.profile') }}"
           class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Profile</span>
            <span class="nav-tooltip" x-show="collapsed">Profile</span>
        </a>

        {{-- PLATFORM (superadmin only) --}}
        @if(auth('admin')->user()?->isSuperAdmin())
        @php $pendingCount = \App\Models\Admin::where('is_superadmin', false)->where('account_status', 'pending')->count(); @endphp
        <div class="nav-section" x-show="!collapsed">Platform</div>

        <a href="{{ route('admin.approvals') }}"
           class="nav-link {{ request()->routeIs('admin.approvals') ? 'active' : '' }}"
           style="position:relative">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Approvals</span>
            @if($pendingCount > 0)
            <span class="nav-badge live-pulse" style="background:#e91e8c;color:white" x-show="!collapsed">{{ $pendingCount }}</span>
            @endif
            <span class="nav-tooltip" x-show="collapsed">Approvals @if($pendingCount > 0)({{ $pendingCount }})@endif</span>
        </a>
        @endif

    </nav>

    {{-- ── User profile + Sign out ── --}}
    @auth('admin')
    <div style="border-top:1px solid rgba(255,255,255,.07);flex-shrink:0">

        {{-- User card (expanded) --}}
        <div x-show="!collapsed" style="padding:12px 14px;display:flex;align-items:center;gap:10px">
            <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#e91e8c,#ad1070);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:800;flex-shrink:0;letter-spacing:-.5px">
                {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0;overflow:hidden">
                <p style="color:rgba(255,255,255,.9);font-size:12.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3">{{ auth('admin')->user()->name }}</p>
                <p style="color:rgba(255,255,255,.38);font-size:11px;line-height:1.3">{{ ucfirst(auth('admin')->user()->role) }}
                    @if(auth('admin')->user()->isSuperAdmin())
                    <span style="background:rgba(233,30,140,.25);color:#f472b6;font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;vertical-align:middle">SUPER</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- User avatar icon (collapsed) --}}
        <div x-show="collapsed" style="padding:12px 10px;display:flex;justify-content:center">
            <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#e91e8c,#ad1070);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:800;letter-spacing:-.5px">
                {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
            </div>
        </div>

        {{-- Sign out --}}
        <div style="padding:0 10px 12px">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="color:rgba(255,100,100,.65);position:relative">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="!collapsed" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Sign Out</span>
                    <span class="nav-tooltip" x-show="collapsed">Sign Out</span>
                </button>
            </form>
        </div>
    </div>
    @endauth

</aside>

{{-- ══════════════════════════════════════════
     MAIN CONTENT AREA
══════════════════════════════════════════ --}}
<div id="main-content"
     :style="collapsed ? 'padding-left:var(--sidebar-collapsed)' : 'padding-left:var(--sidebar-w)'"
     style="padding-left:0;min-height:100vh;display:flex;flex-direction:column"
     class="lg:pl-[240px]">

    {{-- ── Topbar ── --}}
    <header style="background:white;border-bottom:1px solid #e9eaf0;position:sticky;top:0;z-index:20;box-shadow:0 1px 4px rgba(0,0,0,.04)">
        <div style="display:flex;align-items:center;gap:12px;padding:0 24px;height:60px">

            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen=true"
                    style="background:none;border:none;cursor:pointer;color:#6b7280;padding:6px;border-radius:8px;transition:background .15s"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''"
                    class="lg:hidden">
                <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title breadcrumb --}}
            <div class="hidden lg:block">
                <p style="font-size:13px;font-weight:700;color:#1a0030">
                    @yield('breadcrumb', isset($title) ? $title : 'Dashboard')
                </p>
            </div>

            {{-- Search --}}
            <div style="flex:1;max-width:280px;position:relative;margin-left:auto" class="hidden sm:block">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search..."
                       style="width:100%;border:1.5px solid #e9eaf0;border-radius:10px;padding:7px 12px 7px 32px;font-size:13px;background:#f9fafb;color:#374151;outline:none;transition:border-color .15s"
                       onfocus="this.style.borderColor='#e91e8c';this.style.background='white'"
                       onblur="this.style.borderColor='#e9eaf0';this.style.background='#f9fafb'">
            </div>

            <div style="flex:1"></div>

            {{-- Bell --}}
            @php $bell = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
            <div style="position:relative">
                <button style="width:38px;height:38px;border-radius:10px;border:1.5px solid #e9eaf0;display:flex;align-items:center;justify-content:center;cursor:pointer;background:white;transition:all .15s"
                        onmouseover="this.style.borderColor='#e91e8c';this.style.background='#fff5fb'"
                        onmouseout="this.style.borderColor='#e9eaf0';this.style.background='white'">
                    <svg style="width:17px;height:17px;color:#6b7280" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
                @if($bell > 0)
                <span style="position:absolute;top:-4px;right:-4px;width:17px;height:17px;background:#e91e8c;color:white;font-size:9px;font-weight:800;border-radius:50%;display:flex;align-items:center;justify-content:center" class="live-pulse">{{ $bell > 9 ? '9+' : $bell }}</span>
                @endif
            </div>

            {{-- User info --}}
            @auth('admin')
            <a href="{{ route('admin.profile') }}"
               style="display:flex;align-items:center;gap:10px;padding:6px 12px 6px 6px;border-radius:10px;border:1.5px solid #e9eaf0;background:white;cursor:pointer;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#e91e8c';this.style.background='#fff5fb'"
               onmouseout="this.style.borderColor='#e9eaf0';this.style.background='white'">
                <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#e91e8c,#ad1070);display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:800;flex-shrink:0;letter-spacing:-.5px">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
                </div>
                <div style="min-width:0" class="hidden sm:block">
                    <p style="font-size:12.5px;font-weight:700;color:#1a0030;line-height:1.2;white-space:nowrap">{{ Str::words(auth('admin')->user()->name, 1, '') }}</p>
                    <p style="font-size:11px;color:#9ca3af;line-height:1.2">{{ ucfirst(auth('admin')->user()->role) }}</p>
                </div>
                <svg style="width:13px;height:13px;color:#d1d5db;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden sm:block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </a>
            @endauth

        </div>
    </header>

    {{-- Pending approval banner --}}
    @auth('admin')
    @if(auth('admin')->user()->isPending())
    <div style="background:#fffbeb;border-bottom:1px solid #fde68a;padding:11px 24px;display:flex;align-items:center;gap:12px">
        <svg style="width:17px;height:17px;color:#d97706;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p style="font-size:13px;color:#78350f;flex:1">
            Your organizer account is currently <strong>pending admin approval</strong>. You can create event drafts, but you won't be able to publish until approved.
        </p>
        <span style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;white-space:nowrap;flex-shrink:0">
            ⏳ PENDING
        </span>
    </div>
    @endif
    @endauth

    {{-- Flash messages --}}
    @if(session('success'))
    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4500)" x-cloak
         style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;padding:11px 24px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#166534">
        <svg style="width:16px;height:16px;color:#22c55e;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border-bottom:1px solid #fecaca;padding:11px 24px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#dc2626">
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
