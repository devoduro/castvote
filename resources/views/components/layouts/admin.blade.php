<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — CastVote Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @livewireStyles

    {{-- Apply collapsed state before paint to avoid layout flash --}}
    <script>
        (function() {
            if (JSON.parse(localStorage.getItem('cv_sb') || 'false')) {
                document.documentElement.classList.add('sb-off');
            }
        })();
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; background: #f0f2f5; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar sizing (CSS-driven, no Alpine `:style`) ── */
        :root { --sw: 240px; --sc: 68px; }

        #cv-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sw);
            background: #1c2434;
            display: flex; flex-direction: column;
            z-index: 50; overflow: hidden;
            transition: width .26s cubic-bezier(.4,0,.2,1), transform .26s cubic-bezier(.4,0,.2,1);
            transform: translateX(-100%);
        }
        #cv-main {
            margin-left: 0;
            transition: margin-left .26s cubic-bezier(.4,0,.2,1);
            min-height: 100vh; display: flex; flex-direction: column;
        }

        @media (min-width: 1024px) {
            #cv-sidebar { transform: translateX(0); }
            #cv-main    { margin-left: var(--sw); }
        }

        html.sb-off #cv-sidebar { width: var(--sc); }
        html.sb-off #cv-main    { margin-left: var(--sc); }
        html.sb-mobile-open #cv-sidebar { transform: translateX(0); box-shadow: 0 0 50px rgba(0,0,0,.4); }

        /* ── Nav links ── */
        .nav-link {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 8px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,.5);
            transition: background .15s, color .15s;
            text-decoration: none; white-space: nowrap;
            width: 100%; border: none; background: transparent;
            cursor: pointer; text-align: left; position: relative;
        }
        .nav-link:hover  { background: rgba(255,255,255,.07); color: rgba(255,255,255,.85); }
        .nav-link.active {
            background: #4361ee;
            color: #fff; font-weight: 600;
            box-shadow: 0 4px 12px rgba(67,97,238,.35);
        }
        .nav-link .ni { width: 15px; height: 15px; flex-shrink: 0; }

        /* Colored icon boxes */
        .nav-icon-box {
            width: 28px; height: 28px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .15s, color .15s;
        }
        .nav-link.active .nav-icon-box { background: rgba(255,255,255,.2) !important; color: white !important; }

        .nav-sec {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,.18);
            padding: 0 10px; margin: 18px 0 3px;
            white-space: nowrap; overflow: hidden;
        }
        .nav-badge {
            margin-left: auto; font-size: 10px; font-weight: 700;
            padding: 1px 7px; border-radius: 20px; flex-shrink: 0; line-height: 1.7;
        }
        .nav-tip {
            position: absolute; left: 62px; top: 50%; transform: translateY(-50%);
            background: #0f172a; color: #fff; font-size: 12px; font-weight: 600;
            padding: 5px 11px; border-radius: 7px; white-space: nowrap;
            opacity: 0; pointer-events: none; transition: opacity .12s;
            box-shadow: 0 4px 14px rgba(0,0,0,.5); z-index: 200;
            border: 1px solid rgba(255,255,255,.08);
        }
        html.sb-off .nav-link:hover .nav-tip { opacity: 1; }
        html:not(.sb-off) .nav-tip { display: none; }

        /* Hide text/labels when collapsed */
        html.sb-off .nav-label,
        html.sb-off .nav-badge,
        html.sb-off .nav-sec,
        html.sb-off .brand-text,
        html.sb-off .user-info { display: none !important; }

        #cv-nav::-webkit-scrollbar { width: 3px; }
        #cv-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 3px; }

        /* Desktop toggle button */
        .lg-flex { display: none !important; }
        @media (min-width: 1024px) { .lg-flex { display: flex !important; } }
        .mobile-only { display: flex; }
        @media (min-width: 1024px) { .mobile-only { display: none !important; } }
        @media (min-width: 1024px) { #cv-hamburger { display: none !important; } }

        /* Collapsed-only avatar */
        #cv-avatar-sm { display: none; }
        html.sb-off #cv-avatar-sm { display: flex; }
        html.sb-off .user-info { display: none !important; }

        /* Chevron flip */
        html.sb-off #cv-chevron { transform: rotate(180deg); }

        @keyframes pulse2 { 0%,100%{opacity:1} 50%{opacity:.5} }
        .live-dot { animation: pulse2 2s infinite; }
    </style>
</head>
<body>

{{-- Mobile overlay --}}
<div id="cv-overlay" onclick="closeMobileSidebar()"
     style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.6);z-index:40;backdrop-filter:blur(4px)"></div>

{{-- ═══════════════════════════ SIDEBAR ═══════════════════════════ --}}
<aside id="cv-sidebar">

    {{-- Brand --}}
    <div style="padding:0 12px;height:64px;display:flex;align-items:center;gap:10px;flex-shrink:0;border-bottom:1px solid rgba(255,255,255,.05)">
        <div style="width:36px;height:36px;border-radius:10px;background:#4361ee;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:900;color:white;font-size:16px;letter-spacing:-1px;box-shadow:0 4px 14px rgba(67,97,238,.4)">
            CV
        </div>
        <div class="brand-text" style="flex:1;min-width:0;overflow:hidden">
            <p style="color:white;font-weight:800;font-size:14.5px;line-height:1.2;white-space:nowrap">CastVote</p>
            <p style="color:rgba(255,255,255,.3);font-size:10.5px;font-weight:500;white-space:nowrap">Admin Panel</p>
        </div>
        {{-- Desktop collapse --}}
        <button onclick="toggleSidebar()" id="cv-toggle-btn"
                style="width:28px;height:28px;flex-shrink:0;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);cursor:pointer;border-radius:7px;color:rgba(255,255,255,.4);transition:all .15s"
                class="lg-flex" onmouseover="this.style.background='rgba(255,255,255,.12)';this.style.color='white'"
                onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='rgba(255,255,255,.4)'"
                style="display:flex;align-items:center;justify-content:center">
            <svg id="cv-chevron" style="width:13px;height:13px;transition:transform .26s" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        {{-- Mobile close --}}
        <button onclick="closeMobileSidebar()" class="mobile-only brand-text"
                style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.4);padding:4px">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav id="cv-nav" style="flex:1;padding:8px 10px;overflow-y:auto;overflow-x:hidden">

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(67,97,238,.15);color:#4361ee">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </span>
            <span class="nav-label">Dashboard</span>
            <span class="nav-tip">Dashboard</span>
        </a>

        <div class="nav-sec">Events</div>

        <a href="{{ route('admin.events.index') }}"
           class="nav-link {{ request()->routeIs('admin.events.index') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(249,115,22,.15);color:#f97316">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </span>
            <span class="nav-label">All Events</span>
            @php $liveCount = \App\Models\Event::where('organization_id', auth('admin')->user()?->organization_id)->where('status','live')->count(); @endphp
            @if($liveCount)
            <span class="nav-badge live-dot" style="background:rgba(34,197,94,.2);color:#4ade80">{{ $liveCount }}</span>
            @endif
            <span class="nav-tip">All Events{{ $liveCount ? " ($liveCount live)" : '' }}</span>
        </a>

        <a href="{{ route('admin.events.create') }}"
           class="nav-link {{ request()->routeIs('admin.events.create') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(34,197,94,.15);color:#22c55e">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </span>
            <span class="nav-label">Create Event</span>
            <span class="nav-tip">Create Event</span>
        </a>

        <a href="{{ route('admin.nominations') }}"
           class="nav-link {{ request()->routeIs('admin.nominations') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(124,58,237,.15);color:#7c3aed">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </span>
            <span class="nav-label">Nominations</span>
            <span class="nav-tip">Nominations</span>
        </a>

        <a href="{{ route('admin.vote-results') }}"
           class="nav-link {{ request()->routeIs('admin.vote-results') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(67,97,238,.15);color:#4361ee">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            <span class="nav-label">Vote Results</span>
            <span class="nav-tip">Vote Results</span>
        </a>

        <div class="nav-sec">Finance</div>

        <a href="{{ route('admin.transactions') }}"
           class="nav-link {{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(34,197,94,.15);color:#22c55e">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </span>
            <span class="nav-label">Transactions</span>
            @php $pending = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp
            @if($pending)
            <span class="nav-badge" style="background:rgba(251,191,36,.2);color:#fbbf24">{{ $pending }}</span>
            @endif
            <span class="nav-tip">Transactions{{ $pending ? " ($pending pending)" : '' }}</span>
        </a>

        <a href="{{ route('admin.earnings') }}"
           class="nav-link {{ request()->routeIs('admin.earnings') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(249,115,22,.15);color:#f97316">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <span class="nav-label">Earnings</span>
            <span class="nav-tip">Earnings</span>
        </a>

        <div class="nav-sec">Account</div>

        <a href="{{ route('admin.audit') }}"
           class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(124,58,237,.15);color:#7c3aed">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <span class="nav-label">Audit Log</span>
            <span class="nav-tip">Audit Log</span>
        </a>

        <a href="{{ route('admin.profile') }}"
           class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(67,97,238,.15);color:#4361ee">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </span>
            <span class="nav-label">Profile</span>
            <span class="nav-tip">Profile</span>
        </a>

        @if(auth('admin')->user()?->isSuperAdmin())
        @php $pendingCount = \App\Models\Admin::where('is_superadmin', false)->where('account_status', 'pending')->count(); @endphp
        <div class="nav-sec">Platform</div>
        <a href="{{ route('admin.approvals') }}"
           class="nav-link {{ request()->routeIs('admin.approvals') ? 'active' : '' }}">
            <span class="nav-icon-box" style="background:rgba(239,68,68,.15);color:#ef4444">
                <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <span class="nav-label">Approvals</span>
            @if($pendingCount > 0)
            <span class="nav-badge live-dot" style="background:#4361ee;color:white">{{ $pendingCount }}</span>
            @endif
            <span class="nav-tip">Approvals{{ ($pendingCount ?? 0) > 0 ? " ($pendingCount)" : '' }}</span>
        </a>
        @endif

    </nav>

    {{-- User card --}}
    @auth('admin')
    <div style="border-top:1px solid rgba(255,255,255,.05);padding:10px;flex-shrink:0">

        {{-- Full user info (expanded) --}}
        <div class="user-info" style="display:flex;align-items:center;gap:9px;padding:6px 4px 10px">
            <div style="width:33px;height:33px;border-radius:9px;background:#4361ee;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:800;flex-shrink:0">
                {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0;overflow:hidden">
                <p style="color:rgba(255,255,255,.88);font-size:12.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth('admin')->user()->name }}</p>
                <p style="color:rgba(255,255,255,.3);font-size:10.5px;font-weight:500;text-transform:uppercase;letter-spacing:.04em">
                    @if(auth('admin')->user()->isSuperAdmin())
                    Superadmin
                    @else
                    {{ ucfirst(auth('admin')->user()->role ?? 'Admin') }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Avatar only (collapsed) --}}
        <div id="cv-avatar-sm" style="justify-content:center;padding:2px 0 10px">
            <div style="width:33px;height:33px;border-radius:9px;background:#4361ee;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:800">
                {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
            </div>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="color:rgba(255,120,120,.6)">
                <span class="nav-icon-box" style="background:rgba(239,68,68,.12);color:#ef4444">
                    <svg class="ni" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </span>
                <span class="nav-label">Sign Out</span>
                <span class="nav-tip">Sign Out</span>
            </button>
        </form>
    </div>
    @endauth

</aside>

{{-- ═══════════════════════════ MAIN ═══════════════════════════ --}}
<div id="cv-main">

    {{-- Topbar --}}
    <header style="background:white;border-bottom:1px solid #e8eaf0;position:sticky;top:0;z-index:30;height:64px;display:flex;align-items:center;padding:0 22px;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,.04)">

        {{-- Mobile hamburger --}}
        <button onclick="openMobileSidebar()" id="cv-hamburger"
                style="background:none;border:none;cursor:pointer;color:#64748b;padding:6px;border-radius:8px;display:flex;flex-shrink:0">
            <svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Search --}}
        <div style="position:relative;width:240px;flex-shrink:0">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Search..."
                   style="width:100%;border:1.5px solid #e8eaf0;border-radius:10px;padding:8px 12px 8px 33px;font-size:13px;background:#f8fafc;color:#334155;outline:none;font-family:inherit;transition:all .15s"
                   onfocus="this.style.borderColor='#4361ee';this.style.background='white'"
                   onblur="this.style.borderColor='#e8eaf0';this.style.background='#f8fafc'">
        </div>

        <div style="flex:1"></div>

        {{-- Topbar icons --}}
        @php $bell = \App\Models\Payment::whereHas('event', fn($q) => $q->where('organization_id', auth('admin')->user()?->organization_id))->where('status','pending')->count(); @endphp

        {{-- Grid view icon --}}
        <button style="width:38px;height:38px;border-radius:10px;border:1.5px solid #e8eaf0;display:flex;align-items:center;justify-content:center;cursor:pointer;background:white;transition:all .15s;flex-shrink:0"
                onmouseover="this.style.borderColor='#4361ee';this.style.background='#f0f4ff'" onmouseout="this.style.borderColor='#e8eaf0';this.style.background='white'">
            <svg style="width:16px;height:16px;color:#64748b" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </button>

        {{-- Bell --}}
        <div style="position:relative;flex-shrink:0">
            <button style="width:38px;height:38px;border-radius:10px;border:1.5px solid #e8eaf0;display:flex;align-items:center;justify-content:center;cursor:pointer;background:white;transition:all .15s"
                    onmouseover="this.style.borderColor='#4361ee';this.style.background='#f0f4ff'" onmouseout="this.style.borderColor='#e8eaf0';this.style.background='white'">
                <svg style="width:16px;height:16px;color:#64748b" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button>
            @if($bell > 0)
            <span style="position:absolute;top:-4px;right:-4px;width:17px;height:17px;background:#ef4444;color:white;font-size:9px;font-weight:800;border-radius:50%;display:flex;align-items:center;justify-content:center">{{ $bell > 9 ? '9+' : $bell }}</span>
            @endif
        </div>

        {{-- User chip --}}
        @auth('admin')
        <a href="{{ route('admin.profile') }}"
           style="display:flex;align-items:center;gap:9px;padding:5px 14px 5px 5px;border-radius:10px;border:1.5px solid #e8eaf0;background:white;cursor:pointer;text-decoration:none;transition:all .15s;flex-shrink:0"
           onmouseover="this.style.borderColor='#4361ee';this.style.background='#f0f4ff'" onmouseout="this.style.borderColor='#e8eaf0';this.style.background='white'">
            <div style="width:30px;height:30px;border-radius:8px;background:#4361ee;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:800;flex-shrink:0">
                {{ strtoupper(substr(auth('admin')->user()->name, 0, 2)) }}
            </div>
            <div style="min-width:0">
                <p style="font-size:12.5px;font-weight:700;color:#1e293b;line-height:1.2;white-space:nowrap">{{ Str::words(auth('admin')->user()->name, 1, '') }}</p>
                <p style="font-size:10.5px;color:#94a3b8;line-height:1.2;text-transform:uppercase;letter-spacing:.03em">
                    @if(auth('admin')->user()->isSuperAdmin()) Superadmin @else {{ ucfirst(auth('admin')->user()->role ?? 'Admin') }} @endif
                </p>
            </div>
            <svg style="width:12px;height:12px;color:#cbd5e1;flex-shrink:0;margin-left:2px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </a>
        @endauth
    </header>

    {{-- Breadcrumb bar --}}
    <div style="background:#f8fafc;border-bottom:1px solid #e8eaf0;padding:7px 22px;display:flex;align-items:center;gap:6px">
        <svg style="width:13px;height:13px;color:#94a3b8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span style="font-size:11.5px;color:#94a3b8">CastVote</span>
        <span style="font-size:11.5px;color:#cbd5e1">/</span>
        <span style="font-size:11.5px;color:#64748b;font-weight:600">{{ $title ?? 'Dashboard' }}</span>
    </div>

    {{-- Pending banner --}}
    @auth('admin')
    @if(auth('admin')->user()->isPending())
    <div style="background:#fffbeb;border-bottom:1px solid #fde68a;padding:10px 22px;display:flex;align-items:center;gap:10px">
        <svg style="width:15px;height:15px;color:#d97706;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p style="font-size:13px;color:#78350f;flex:1">Your account is <strong>pending admin approval</strong>. Event publishing is disabled until approved.</p>
        <span style="background:#fef3c7;border:1px solid #fcd34d;color:#92400e;font-size:11px;font-weight:700;padding:2px 12px;border-radius:20px;white-space:nowrap;flex-shrink:0">⏳ PENDING</span>
    </div>
    @endif
    @endauth

    {{-- Flash --}}
    @if(session('success'))
    <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4500)" x-cloak
         style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;padding:10px 22px;display:flex;align-items:center;gap:9px;font-size:13px;color:#166534">
        <svg style="width:15px;height:15px;color:#22c55e;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border-bottom:1px solid #fecaca;padding:10px 22px;display:flex;align-items:center;gap:9px;font-size:13px;color:#dc2626">
        <svg style="width:15px;height:15px;color:#ef4444;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Page content --}}
    <main style="flex:1;padding:24px 22px">
        {{ $slot }}
    </main>

</div>

<script>
    function toggleSidebar() {
        const collapsed = document.documentElement.classList.toggle('sb-off');
        localStorage.setItem('cv_sb', collapsed);
    }
    function openMobileSidebar() {
        document.documentElement.classList.add('sb-mobile-open');
        document.getElementById('cv-overlay').style.display = 'block';
    }
    function closeMobileSidebar() {
        document.documentElement.classList.remove('sb-mobile-open');
        document.getElementById('cv-overlay').style.display = 'none';
    }
</script>

@livewireScripts
</body>
</html>
