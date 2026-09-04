<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Organizer Account — ClickVote</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;display:flex;min-height:100vh;background:#faf9fc}

        .left{
            width:44%;background:#241038;
            padding:36px 48px;display:flex;flex-direction:column;position:relative;overflow:hidden;
        }
        .left::before{
            content:'';position:absolute;inset:0;
            background-image:radial-gradient(circle,rgba(255,255,255,.04) 1px,transparent 1px);
            background-size:24px 24px;pointer-events:none;
        }
        .left::after{
            content:'';position:absolute;bottom:-80px;left:-60px;
            width:240px;height:240px;border-radius:50%;
            background:radial-gradient(circle,rgba(225,29,116,.18) 0%,transparent 70%);
            pointer-events:none;
        }

        .right{
            flex:1;background:white;display:flex;align-items:center;justify-content:center;
            padding:36px 48px;overflow-y:auto;
        }
        .form-wrap{width:100%;max-width:420px}

        .field-group{margin-bottom:15px}
        .field-label{font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px;display:block}
        .input-icon-wrap{position:relative}
        .input-icon-wrap .icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            width:16px;height:16px;color:#9ca3af;pointer-events:none;
        }
        .input-field{
            width:100%;border:1.5px solid #e5e7eb;border-radius:10px;
            padding:11px 14px 11px 42px;font-size:13.5px;font-family:'Inter',sans-serif;
            color:#1e293b;background:#f8fafc;transition:all .15s;outline:none;
        }
        .input-field:focus{border-color:#e11d74;background:#fff;box-shadow:0 0 0 3px rgba(225,29,116,.1)}
        .input-field.error{border-color:#ef4444;background:#fff5f5}
        .field-error{color:#ef4444;font-size:11.5px;margin-top:3px}
        .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px}

        .submit-btn{
            width:100%;padding:13px;border:none;border-radius:10px;
            background:#e11d74;
            color:white;font-size:14.5px;font-weight:700;font-family:'Inter',sans-serif;
            cursor:pointer;margin-top:4px;
            display:flex;align-items:center;justify-content:center;gap:8px;
            transition:background .15s,transform .1s,box-shadow .15s;
            box-shadow:0 4px 14px rgba(225,29,116,.35);
        }
        .submit-btn:hover{background:#3451d1;transform:translateY(-1px);box-shadow:0 6px 20px rgba(225,29,116,.45)}

        .back-pill{
            display:inline-flex;align-items:center;gap:6px;
            border:1.5px solid rgba(255,255,255,.12);border-radius:20px;
            padding:7px 14px;color:rgba(255,255,255,.55);font-size:13px;font-weight:600;
            text-decoration:none;margin-bottom:40px;position:relative;z-index:1;
            transition:all .15s;width:fit-content;
        }
        .back-pill:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)}

        @media(max-width:768px){
            .left{display:none}
            .right{padding:32px 20px}
        }
        /* Phones: nothing may exceed the viewport width. */
        @media(max-width:520px){
            .right{padding:26px 16px}
            .form-wrap{max-width:100%}
            [style*="grid-template-columns"]{grid-template-columns:1fr !important}
        }
    </style>
</head>
<body>

{{-- ── LEFT PANEL ── --}}
<div class="left">
    <a href="{{ route('vote.index') }}" class="back-pill">
        <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Home
    </a>

    <div style="flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:1">

        <div style="width:52px;height:52px;background:#e11d74;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:28px;box-shadow:0 6px 20px rgba(225,29,116,.4);font-weight:900;color:white;font-size:18px;letter-spacing:-1px">
            CV
        </div>

        <h1 style="color:white;font-size:clamp(26px,3vw,36px);font-weight:900;line-height:1.15;margin-bottom:14px">
            Host World-Class<br>
            <span style="color:#e11d74">Events.</span>
        </h1>
        <p style="color:rgba(255,255,255,.45);font-size:14.5px;line-height:1.7;max-width:310px;margin-bottom:32px">
            Launch your awards scheme or school election in minutes. Real-time analytics, instant payouts, zero stress.
        </p>

        {{-- Features list --}}
        <div style="display:flex;flex-direction:column;gap:12px;max-width:310px">
            @foreach([
                ['Real-time vote tracking & analytics','#e11d74'],
                ['Instant revenue dashboard','#22c55e'],
                ['Mobile money payments (Momo, Vodafone)','#dc6803'],
                ['Free to get started — no setup fee','#a855f7'],
            ] as [$feat,$clr])
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:28px;height:28px;border-radius:8px;background:{{ $clr }}22;border:1px solid {{ $clr }}44;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg style="width:13px;height:13px;color:{{ $clr }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p style="color:rgba(255,255,255,.65);font-size:13px;font-weight:500">{{ $feat }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Live event mockup --}}
    <div style="position:relative;z-index:1;margin-top:24px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px 16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <p style="color:rgba(255,255,255,.6);font-size:11.5px;font-weight:700">SRC Elections 2026</p>
            <span style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.25);color:#4ade80;font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px">LIVE</span>
        </div>
        <div style="display:flex;justify-content:space-between">
            <div><p style="color:rgba(255,255,255,.3);font-size:9.5px;font-weight:600">VOTES</p><p style="color:white;font-weight:800;font-size:15px">3,841</p></div>
            <div style="text-align:right"><p style="color:rgba(255,255,255,.3);font-size:9.5px;font-weight:600">REVENUE</p><p style="color:#e11d74;font-weight:800;font-size:15px">GH₵ 19,205</p></div>
        </div>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right">
    <div class="form-wrap">

        <div style="margin-bottom:22px">
            <h2 style="font-size:24px;font-weight:900;color:#1e293b;margin-bottom:5px">Create Your Account</h2>
            <p style="color:#94a3b8;font-size:13.5px">Get started — your account is activated instantly.</p>
        </div>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:18px;display:flex;align-items:flex-start;gap:8px">
            <svg style="width:14px;height:14px;flex-shrink:0;margin-top:1px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:18px">
            <p style="font-weight:700;margin-bottom:4px">Please fix the following:</p>
            <ul style="padding-left:16px">
                @foreach($errors->all() as $e)<li style="font-size:12.5px;margin-top:2px">{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.register.post') }}">
            @csrf

            {{-- Name --}}
            <div class="field-group">
                <label class="field-label" for="reg-name">Full Name</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <input type="text" id="reg-name" name="name" autocomplete="name" value="{{ old('name') }}" required
                           class="input-field {{ $errors->has('name') ? 'error' : '' }}"
                           placeholder="Kwame Mensah">
                </div>
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Organization --}}
            <div class="field-group">
                <label class="field-label" for="reg-org">Organization Name</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <input type="text" id="reg-org" name="org_name" autocomplete="organization" value="{{ old('org_name') }}" required
                           class="input-field {{ $errors->has('org_name') ? 'error' : '' }}"
                           placeholder="e.g. SRC 2025, Ghana Music Awards">
                </div>
                @error('org_name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div class="field-group">
                <label class="field-label" for="reg-email">Email Address</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input type="email" id="reg-email" name="email" autocomplete="email" value="{{ old('email') }}" required
                           class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="you@example.com">
                </div>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Phone --}}
            <div class="field-group">
                <label class="field-label" for="reg-phone">Phone Number <span style="color:#e11d74">*</span></label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <input type="tel" id="reg-phone" name="phone" autocomplete="tel" required value="{{ old('phone') }}"
                           class="input-field {{ $errors->has('phone') ? 'error' : '' }}"
                           placeholder="024 456 7890">
                </div>
                @error('phone')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div class="field-group" x-data="{show:false}">
                <label class="field-label" for="reg-password">Password</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input type="password" :type="show ? 'text' : 'password'" id="reg-password" name="password" autocomplete="new-password" required
                           class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="Min. 8 characters" style="padding-right:44px">
                    <button type="button" class="eye-btn" @click="show=!show" :aria-label="show ? 'Hide password' : 'Show password'" aria-label="Show password">
                        <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Confirm Password --}}
            <div class="field-group" x-data="{show:false}">
                <label class="field-label" for="reg-password2">Confirm Password</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <input type="password" :type="show ? 'text' : 'password'" id="reg-password2" name="password_confirmation" autocomplete="new-password" required
                           class="input-field" placeholder="Re-enter your password" style="padding-right:44px">
                    <button type="button" class="eye-btn" @click="show=!show" :aria-label="show ? 'Hide password' : 'Show password'" aria-label="Show password">
                        <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            {{-- Terms --}}
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:18px">
                <input type="checkbox" name="terms" id="terms" required
                       style="width:15px;height:15px;margin-top:2px;accent-color:#e11d74;flex-shrink:0;cursor:pointer">
                <label for="terms" style="font-size:12.5px;color:#64748b;line-height:1.55;cursor:pointer">
                    I agree to the
                    <a href="{{ route('vote.privacy') }}" target="_blank" style="color:#e11d74;font-weight:700;text-decoration:none">Terms of Service</a>
                    and
                    <a href="{{ route('vote.privacy') }}" target="_blank" style="color:#e11d74;font-weight:700;text-decoration:none">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="submit-btn">
                Create Account — Get Started Free
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:13px;color:#94a3b8">
            Already have an account?
            <a href="{{ route('admin.login') }}" style="color:#e11d74;font-weight:700;text-decoration:none">Sign In</a>
        </p>

    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
