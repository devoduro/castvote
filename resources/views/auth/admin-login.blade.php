<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CastVote Organizer Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        input:focus { outline: none; }
        .input {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px;
            padding: 11px 16px; font-size: 14px; transition: border-color .15s, box-shadow .15s;
            background: #f8fafc;
        }
        .input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); background: #fff; }
        .input.error { border-color: #ef4444; background: #fff5f5; }
    </style>
</head>
<body style="min-height:100vh;background:linear-gradient(135deg,#060d1a 0%,#0d1526 50%,#162034 100%);display:flex;align-items:center;justify-content:center;padding:24px">

<div style="width:100%;max-width:860px;display:grid;grid-template-columns:1fr 1fr;gap:0;border-radius:24px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.5)">

    {{-- ── Left panel ── --}}
    <div style="background:linear-gradient(160deg,#0d1526 0%,#111827 100%);padding:48px 40px;display:flex;flex-direction:column;justify-content:space-between;border-right:1px solid rgba(255,255,255,.06)">
        <div>
            <a href="{{ route('vote.index') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:48px">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,#ea580c,#f97316);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(234,88,12,.4)">
                    <svg style="width:22px;height:22px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span style="color:white;font-weight:800;font-size:20px;letter-spacing:-.3px">CastVote Ghana</span>
            </a>

            <h1 style="color:white;font-size:28px;font-weight:800;line-height:1.25;margin-bottom:12px">
                The voting platform<br>built for Ghana.
            </h1>
            <p style="color:#64748b;font-size:14px;line-height:1.7;margin-bottom:36px">
                Manage your award shows, AGMs, and elections with real-time USSD + web voting and Paystack payments.
            </p>

            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach([
                    ['USSD *928# + Web voting in one platform', '#10b981'],
                    ['Real-time tally & revenue dashboards', '#3b82f6'],
                    ['Paystack-secured GHS transactions', '#f59e0b'],
                    ['Vote integrity & fraud detection built-in', '#ea580c'],
                ] as [$text, $color])
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $color }};flex-shrink:0;box-shadow:0 0 8px {{ $color }}"></div>
                    <span style="color:#94a3b8;font-size:13px;font-weight:500">{{ $text }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div style="padding:16px;background:rgba(255,255,255,.04);border-radius:14px;border:1px solid rgba(255,255,255,.08);margin-top:32px">
            <p style="color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:6px">USSD Code</p>
            <p style="color:white;font-size:22px;font-weight:800;letter-spacing:1px">*928#</p>
            <p style="color:#475569;font-size:12px;margin-top:4px">Works on all Ghana networks — MTN, Vodafone, AirtelTigo</p>
        </div>
    </div>

    {{-- ── Right panel (form) ── --}}
    <div style="background:white;padding:48px 40px">
        <div style="margin-bottom:32px">
            <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin-bottom:6px">Welcome back</h2>
            <p style="color:#64748b;font-size:14px">
                Don't have an account?
                <a href="{{ route('admin.register') }}" style="color:#ea580c;font-weight:600;text-decoration:none">Create one →</a>
            </p>
        </div>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:12px;padding:14px 16px;font-size:13.5px;margin-bottom:20px">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;border-radius:12px;padding:14px 16px;font-size:13.5px;margin-bottom:20px">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" style="display:flex;flex-direction:column;gap:18px">
            @csrf

            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                    Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="you@example.com"
                       class="input {{ $errors->has('email') ? 'error' : '' }}">
            </div>

            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                    <label style="font-size:13px;font-weight:600;color:#374151">Password</label>
                </div>
                <input type="password" name="password" required
                       placeholder="Your password"
                       class="input {{ $errors->has('password') ? 'error' : '' }}">
            </div>

            <div style="display:flex;align-items:center;gap:10px">
                <input type="checkbox" name="remember" id="remember"
                       style="width:16px;height:16px;accent-color:#ea580c">
                <label for="remember" style="font-size:13px;color:#64748b;cursor:pointer">Keep me signed in</label>
            </div>

            <button type="submit"
                    style="width:100%;padding:13px;border-radius:12px;font-size:15px;font-weight:700;color:white;border:none;cursor:pointer;letter-spacing:-.2px;background:linear-gradient(135deg,#ea580c,#f97316);box-shadow:0 4px 16px rgba(234,88,12,.35);transition:opacity .15s">
                Sign In to Portal
            </button>
        </form>

        <div style="margin-top:32px;padding-top:24px;border-top:1px solid #f1f5f9">
            <p style="color:#94a3b8;font-size:12.5px;text-align:center;line-height:1.6">
                Having trouble signing in?
                <a href="{{ route('vote.index') }}" style="color:#ea580c;font-weight:600;text-decoration:none">Visit home page</a>
                or email
                <a href="mailto:support@castvote.com.gh" style="color:#64748b;text-decoration:none">support@castvote.com.gh</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
