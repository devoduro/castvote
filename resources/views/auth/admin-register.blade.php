<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Organizer Account — CastVote</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;display:flex;min-height:100vh}

        .left{
            width:48%;background:linear-gradient(160deg,#3b0068 0%,#2d0050 40%,#1a0030 100%);
            padding:32px 48px;display:flex;flex-direction:column;position:relative;overflow:hidden;
        }
        .left::before{
            content:'';position:absolute;inset:0;
            background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);
            background-size:22px 22px;pointer-events:none;
        }

        .right{
            flex:1;background:white;display:flex;align-items:center;justify-content:center;
            padding:40px 48px;overflow-y:auto;
        }
        .form-wrap{width:100%;max-width:420px}

        .field-group{margin-bottom:15px}
        .field-label{font-size:12.5px;font-weight:600;color:#374151;margin-bottom:5px;display:block}
        .input-icon-wrap{position:relative}
        .input-icon-wrap .icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            width:16px;height:16px;color:#9ca3af;pointer-events:none;flex-shrink:0;
        }
        .input-field{
            width:100%;border:1.5px solid #e5e7eb;border-radius:10px;
            padding:11px 14px 11px 42px;font-size:13.5px;font-family:'Inter',sans-serif;
            color:#1a0030;background:#f0eef8;transition:all .15s;outline:none;
        }
        .input-field:focus{border-color:#e91e8c;background:#fff;box-shadow:0 0 0 3px rgba(233,30,140,.1)}
        .input-field.error{border-color:#ef4444;background:#fff5f5}
        .field-error{color:#ef4444;font-size:11.5px;margin-top:3px}

        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px}

        .submit-btn{
            width:100%;padding:13px;border:none;border-radius:10px;
            background:linear-gradient(135deg,#2d0050,#3b0068);
            color:white;font-size:14.5px;font-weight:700;font-family:'Inter',sans-serif;
            cursor:pointer;letter-spacing:-.1px;margin-top:4px;
            display:flex;align-items:center;justify-content:center;gap:8px;
            transition:opacity .15s;
        }
        .submit-btn:hover{opacity:.9}

        .back-pill{
            display:inline-flex;align-items:center;gap:6px;
            border:1.5px solid rgba(233,30,140,.5);border-radius:20px;
            padding:7px 14px;color:#e91e8c;font-size:13px;font-weight:600;
            text-decoration:none;margin-bottom:40px;position:relative;z-index:1;
            transition:all .15s;width:fit-content;
        }
        .back-pill:hover{background:rgba(233,30,140,.1)}

        .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px}

        @media(max-width:768px){
            .left{display:none}
            .right{padding:32px 24px}
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
        {{-- Shield icon --}}
        <div style="width:56px;height:56px;background:rgba(255,255,255,.1);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:28px">
            <svg style="width:28px;height:28px;color:#e91e8c" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>

        <h1 style="color:white;font-size:36px;font-weight:900;line-height:1.15;margin-bottom:16px">
            Host World-Class<br>
            <span style="color:#e91e8c">Events.</span>
        </h1>
        <p style="color:rgba(255,255,255,.55);font-size:15px;line-height:1.65;max-width:320px;margin-bottom:32px">
            Launch your awards scheme or school election in minutes. Get real-time analytics, instant payouts, and zero stress.
        </p>

        {{-- Feature mockup card --}}
        <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:20px;max-width:320px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:36px;height:36px;border-radius:10px;background:#e91e8c;display:flex;align-items:center;justify-content:center">
                    <svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p style="color:white;font-weight:700;font-size:13px">Miss Ghana 2026</p>
                    <p style="color:rgba(255,255,255,.4);font-size:11px">3 categories · 24 nominees</p>
                </div>
                <div style="margin-left:auto;background:rgba(16,185,129,.2);border:1px solid rgba(16,185,129,.3);border-radius:20px;padding:3px 10px">
                    <p style="color:#34d399;font-size:10px;font-weight:700">LIVE</p>
                </div>
            </div>
            <div style="height:8px;border-radius:4px;background:rgba(255,255,255,.08);margin-bottom:8px;overflow:hidden">
                <div style="height:100%;width:68%;background:linear-gradient(90deg,#e91e8c,#7c3aed);border-radius:4px"></div>
            </div>
            <div style="height:8px;border-radius:4px;background:rgba(255,255,255,.08);width:45%;overflow:hidden">
                <div style="height:100%;width:100%;background:rgba(255,255,255,.2);border-radius:4px"></div>
            </div>
            <div style="margin-top:14px;display:flex;justify-content:space-between">
                <div>
                    <p style="color:rgba(255,255,255,.4);font-size:10px;font-weight:600">VOTES TODAY</p>
                    <p style="color:white;font-size:18px;font-weight:800">1,284</p>
                </div>
                <div style="text-align:right">
                    <p style="color:rgba(255,255,255,.4);font-size:10px;font-weight:600">REVENUE</p>
                    <p style="color:#e91e8c;font-size:18px;font-weight:800">GHS 6,420</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval notice --}}
    <div style="position:relative;z-index:1;margin-top:24px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:12px 16px">
        <p style="color:rgba(255,255,255,.4);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px">Account Review</p>
        <p style="color:rgba(255,255,255,.65);font-size:12.5px;line-height:1.5">New accounts are reviewed within 24 hours. You'll be notified once approved.</p>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right">
    <div class="form-wrap">
        <h2 style="font-size:26px;font-weight:900;color:#1a0030;margin-bottom:4px">Become an Organizer</h2>
        <p style="color:#9ca3af;font-size:13.5px;margin-bottom:24px">Start managing your events professionally today.</p>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:18px">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:18px">
            <p style="font-weight:700;margin-bottom:3px">Please fix the following:</p>
            <ul style="padding-left:16px">
                @foreach($errors->all() as $e)<li style="font-size:12.5px">{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.register.post') }}">
            @csrf

            {{-- Full Name --}}
            <div class="field-group">
                <label class="field-label">Full Name</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="input-field {{ $errors->has('name') ? 'error' : '' }}"
                           placeholder="Kwame Mensah">
                </div>
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Organization Name --}}
            <div class="field-group">
                <label class="field-label">Organization Name</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <input type="text" name="org_name" value="{{ old('org_name') }}" required
                           class="input-field {{ $errors->has('org_name') ? 'error' : '' }}"
                           placeholder="e.g. SRC 2025, Ghana Music Awards">
                </div>
                @error('org_name')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div class="field-group">
                <label class="field-label">Email Address</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="you@example.com">
                </div>
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Phone --}}
            <div class="field-group">
                <label class="field-label">Phone Number</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="input-field {{ $errors->has('phone') ? 'error' : '' }}"
                           placeholder="024 456 7890">
                </div>
                @error('phone')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div class="field-group" x-data="{show:false}">
                <label class="field-label">Password</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input :type="show ? 'text' : 'password'" name="password" required
                           class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="Min. 8 characters" style="padding-right:44px">
                    <button type="button" class="eye-btn" @click="show=!show">
                        <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            {{-- Confirm Password --}}
            <div class="field-group" x-data="{show:false}">
                <label class="field-label">Confirm Password</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                           class="input-field"
                           placeholder="Re-enter your password" style="padding-right:44px">
                    <button type="button" class="eye-btn" @click="show=!show">
                        <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Terms --}}
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:18px">
                <input type="checkbox" name="terms" id="terms" required
                       style="width:16px;height:16px;margin-top:2px;accent-color:#e91e8c;flex-shrink:0;cursor:pointer">
                <label for="terms" style="font-size:12.5px;color:#6b7280;line-height:1.55;cursor:pointer">
                    I agree to the
                    <a href="{{ route('vote.privacy') }}" target="_blank" style="color:#e91e8c;font-weight:700;text-decoration:none">Terms of Service</a>
                    and
                    <a href="{{ route('vote.privacy') }}" target="_blank" style="color:#e91e8c;font-weight:700;text-decoration:none">Privacy Policy</a>.
                </label>
            </div>

            <button type="submit" class="submit-btn">
                Create Organizer Account
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:13px;color:#9ca3af">
            Already have an account?
            <a href="{{ route('admin.login') }}" style="color:#e91e8c;font-weight:700;text-decoration:none">Log In</a>
        </p>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
