<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CastVote Ghana' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{margin:0;padding:0;font-family:'Inter',sans-serif;background:#f5f4fa;color:#1a0030;-webkit-font-smoothing:antialiased}
        a{color:inherit;text-decoration:none}
        [x-cloak]{display:none!important}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
    </style>
</head>
<body>

{{-- ── Navigation ── --}}
<nav style="background:white;border-bottom:1px solid #f3f4f6;position:sticky;top:0;z-index:50;box-shadow:0 1px 3px rgba(0,0,0,.04)">
    <div style="max-width:1160px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;height:64px">

        {{-- Logo --}}
        <a href="{{ route('vote.index') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none">
            <div style="width:34px;height:34px;background:linear-gradient(135deg,#e91e8c,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center">
                <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span style="font-size:18px;font-weight:800;color:#1a0030;letter-spacing:-.3px">Cast<span style="color:#e91e8c">Vote</span></span>
        </a>

        {{-- Nav links --}}
        <div style="display:flex;align-items:center;gap:28px">
            <a href="{{ route('vote.index') }}"
               style="font-size:14px;font-weight:600;color:#1a0030;opacity:{{ request()->routeIs('vote.index') ? '1' : '.6' }};transition:opacity .15s"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='{{ request()->routeIs('vote.index') ? '1' : '.6' }}'">
                Home
            </a>
            <a href="{{ route('vote.index') }}#how-it-works"
               style="font-size:14px;font-weight:600;color:#1a0030;opacity:.6;transition:opacity .15s"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.6'">
                How It Works
            </a>
            <a href="{{ route('vote.index') }}"
               style="font-size:14px;font-weight:600;color:#1a0030;opacity:.6;transition:opacity .15s"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.6'">
                Voting
            </a>
            <a href="{{ route('vote.privacy') }}"
               style="font-size:14px;font-weight:600;color:#1a0030;opacity:.6;transition:opacity .15s"
               onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.6'">
                Privacy
            </a>
        </div>

        {{-- Right side --}}
        <div style="display:flex;align-items:center;gap:12px">
            <a href="{{ route('admin.login') }}"
               style="font-size:13.5px;font-weight:600;color:#6b7280;padding:8px 16px;border-radius:8px;transition:background .15s"
               onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''">
                Sign In
            </a>
            <a href="{{ route('admin.register') }}"
               style="display:inline-flex;align-items:center;gap:6px;background:white;border:1.5px solid #1a0030;color:#1a0030;font-size:13.5px;font-weight:700;padding:8px 18px;border-radius:10px;transition:all .15s"
               onmouseover="this.style.background='#1a0030';this.style.color='white'" onmouseout="this.style.background='white';this.style.color='#1a0030'">
                Create Event
            </a>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
<div style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;color:#166534;font-size:14px;padding:12px 20px;text-align:center;font-weight:500">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fff5f5;border-bottom:1px solid #fecaca;color:#dc2626;font-size:14px;padding:12px 20px;text-align:center;font-weight:500">
    {{ session('error') }}
</div>
@endif

{{-- Page content --}}
{{ $slot }}

{{-- ── Footer ── --}}
<footer style="background:#1a0030;color:rgba(255,255,255,.55);margin-top:0">
    <div style="max-width:1160px;margin:0 auto;padding:48px 20px 32px">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:40px;margin-bottom:40px">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:32px;height:32px;background:linear-gradient(135deg,#e91e8c,#7c3aed);border-radius:8px;display:flex;align-items:center;justify-content:center">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span style="font-size:17px;font-weight:800;color:white">CastVote</span>
                </div>
                <p style="font-size:13.5px;line-height:1.7;max-width:280px">Ghana's trusted platform for award shows, corporate AGMs, and student elections. Secure, transparent, instant.</p>
            </div>
            <div>
                <h4 style="color:white;font-weight:700;font-size:13.5px;margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em">Vote via USSD</h4>
                <p style="font-size:13.5px;line-height:1.7">No internet? No problem.<br>Dial <span style="color:#e91e8c;font-weight:700;font-size:15px">*928#</span> on any Ghana network.</p>
                <p style="font-size:12px;margin-top:6px;color:rgba(255,255,255,.3)">MTN · Telecel · AirtelTigo</p>
            </div>
            <div>
                <h4 style="color:white;font-weight:700;font-size:13.5px;margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em">Legal</h4>
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px">
                    <li><a href="{{ route('vote.privacy') }}" style="font-size:13.5px;color:rgba(255,255,255,.55);transition:color .15s" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.55)'">Privacy Policy</a></li>
                    <li><a href="{{ route('admin.register') }}" style="font-size:13.5px;color:rgba(255,255,255,.55);transition:color .15s" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.55)'">Organizer Registration</a></li>
                </ul>
            </div>
        </div>
        <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:24px;text-align:center;font-size:12.5px">
            &copy; {{ date('Y') }} CastVote Ghana. Payments secured by Paystack. Ghana DPA Act 843 compliant.
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
