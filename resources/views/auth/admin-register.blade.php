<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Organizer Account — CastVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        input:focus { outline: none; }
        .input {
            width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px;
            padding: 10px 16px; font-size: 14px; transition: border-color .15s, box-shadow .15s;
            background: #f8fafc;
        }
        .input:focus { border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,.12); background: #fff; }
        .input.error { border-color: #ef4444; background: #fff5f5; }
    </style>
</head>
<body style="min-height:100vh;background:linear-gradient(135deg,#060d1a 0%,#0d1526 50%,#162034 100%);display:flex;align-items:center;justify-content:center;padding:24px">

<div style="width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;gap:0;border-radius:24px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.5)"
     class="md:grid-cols-2">

    {{-- ── Left panel ── --}}
    <div style="background:linear-gradient(160deg,#ea580c 0%,#c2410c 60%,#7c2d12 100%);padding:48px 40px;display:flex;flex-direction:column;justify-content:space-between;">
        {{-- Brand --}}
        <div>
            <a href="{{ route('vote.index') }}" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:48px">
                <div style="width:40px;height:40px;background:rgba(255,255,255,.2);border-radius:12px;display:flex;align-items:center;justify-content:center">
                    <svg style="width:22px;height:22px;color:white" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span style="color:white;font-weight:800;font-size:20px;letter-spacing:-.3px">CastVote Ghana</span>
            </a>

            <h1 style="color:white;font-size:32px;font-weight:800;line-height:1.2;margin-bottom:16px">
                Launch your<br>voting event<br>today.
            </h1>
            <p style="color:rgba(255,255,255,.75);font-size:15px;line-height:1.6;margin-bottom:32px">
                Create your organizer account to manage award shows, corporate AGMs, and student elections — with USSD + web voting built in.
            </p>
        </div>

        {{-- Feature list --}}
        <div style="space-y:12px">
            @foreach([
                ['Web + USSD voting — reaches every voter', '📱'],
                ['Real-time results & revenue dashboard', '📊'],
                ['Paystack-powered secure payments (GHS)', '🔒'],
                ['Fraud detection & vote integrity audit', '🛡️'],
            ] as [$text, $icon])
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <span style="font-size:18px">{{ $icon }}</span>
                <span style="color:rgba(255,255,255,.85);font-size:13.5px;font-weight:500">{{ $text }}</span>
            </div>
            @endforeach

            <div style="margin-top:24px;padding:16px;background:rgba(255,255,255,.1);border-radius:14px;border:1px solid rgba(255,255,255,.15)">
                <p style="color:rgba(255,255,255,.6);font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:4px">Approval required</p>
                <p style="color:rgba(255,255,255,.85);font-size:13px;line-height:1.5">
                    New accounts are reviewed within 24 hours. You'll be notified once your account is activated.
                </p>
            </div>
        </div>
    </div>

    {{-- ── Right panel (form) ── --}}
    <div style="background:white;padding:48px 40px;overflow-y:auto;max-height:90vh">
        <div style="margin-bottom:32px">
            <h2 style="font-size:24px;font-weight:800;color:#0f172a;margin-bottom:6px">Create your account</h2>
            <p style="color:#64748b;font-size:14px">
                Already have an account?
                <a href="{{ route('admin.login') }}" style="color:#ea580c;font-weight:600;text-decoration:none">Sign in →</a>
            </p>
        </div>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:12px;padding:14px 16px;font-size:13.5px;margin-bottom:20px">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;border-radius:12px;padding:14px 16px;font-size:13.5px;margin-bottom:20px">
            <p style="font-weight:700;margin-bottom:4px">Please fix the following:</p>
            <ul style="list-style:disc;padding-left:16px;space-y:2px">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.register.post') }}" style="display:flex;flex-direction:column;gap:18px">
            @csrf

            {{-- Organization --}}
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                    Organization / Company Name <span style="color:#ef4444">*</span>
                </label>
                <input type="text" name="org_name" value="{{ old('org_name') }}" required
                       placeholder="e.g. Ghana Music Awards, UG SRC"
                       class="input {{ $errors->has('org_name') ? 'error' : '' }}">
                @error('org_name') <p style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>

            {{-- Full name --}}
            <div>
                <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                    Your Full Name <span style="color:#ef4444">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="John Mensah"
                       class="input {{ $errors->has('name') ? 'error' : '' }}">
                @error('name') <p style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
            </div>

            {{-- Email + Phone grid --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                        Email Address <span style="color:#ef4444">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="you@example.com"
                           class="input {{ $errors->has('email') ? 'error' : '' }}">
                    @error('email') <p style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                        Phone Number
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="0244 123 456"
                           class="input {{ $errors->has('phone') ? 'error' : '' }}">
                    @error('phone') <p style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Password + confirm --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                        Password <span style="color:#ef4444">*</span>
                    </label>
                    <input type="password" name="password" required
                           placeholder="Min. 8 characters"
                           class="input {{ $errors->has('password') ? 'error' : '' }}">
                    @error('password') <p style="color:#ef4444;font-size:12px;margin-top:4px">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px">
                        Confirm Password <span style="color:#ef4444">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Repeat password"
                           class="input">
                </div>
            </div>

            {{-- Terms --}}
            <div style="display:flex;align-items:flex-start;gap:10px">
                <input type="checkbox" name="terms" id="terms" required
                       style="width:16px;height:16px;margin-top:2px;accent-color:#ea580c;flex-shrink:0">
                <label for="terms" style="font-size:13px;color:#64748b;line-height:1.5;cursor:pointer">
                    I agree to the
                    <a href="{{ route('vote.privacy') }}" target="_blank" style="color:#ea580c;font-weight:600;text-decoration:none">Privacy Policy</a>
                    and understand my account requires admin approval before activation.
                </label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    style="width:100%;padding:13px;border-radius:12px;font-size:15px;font-weight:700;color:white;border:none;cursor:pointer;letter-spacing:-.2px;transition:opacity .15s;background:linear-gradient(135deg,#ea580c,#f97316);box-shadow:0 4px 16px rgba(234,88,12,.35)">
                Create Organizer Account
            </button>
        </form>

        <p style="color:#94a3b8;font-size:12px;text-align:center;margin-top:24px;line-height:1.5">
            By creating an account you agree to our terms. CastVote Ghana is compliant with the<br>
            Ghana Data Protection Act 2012 (Act 843).
        </p>
    </div>
</div>

</body>
</html>
