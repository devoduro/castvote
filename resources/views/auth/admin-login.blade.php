<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CastVote Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;display:flex;min-height:100vh;background:#f0f2f5}

        /* Left panel */
        .left{
            width:48%;background:#1c2434;
            padding:36px 48px;display:flex;flex-direction:column;position:relative;overflow:hidden;
        }
        .left::before{
            content:'';position:absolute;inset:0;
            background-image:radial-gradient(circle,rgba(255,255,255,.04) 1px,transparent 1px);
            background-size:24px 24px;pointer-events:none;
        }
        /* Blue glow orbs */
        .left::after{
            content:'';position:absolute;top:-80px;right:-80px;
            width:280px;height:280px;border-radius:50%;
            background:radial-gradient(circle,rgba(67,97,238,.2) 0%,transparent 70%);
            pointer-events:none;
        }

        /* Right panel */
        .right{
            flex:1;background:white;display:flex;align-items:center;justify-content:center;padding:48px;
        }
        .form-wrap{width:100%;max-width:400px}

        .field-group{margin-bottom:18px}
        .field-label{font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;display:block}
        .field-label-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}

        .input-icon-wrap{position:relative}
        .input-icon-wrap .icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            width:16px;height:16px;color:#9ca3af;pointer-events:none;
        }
        .input-field{
            width:100%;border:1.5px solid #e5e7eb;border-radius:10px;
            padding:12px 14px 12px 42px;font-size:14px;font-family:'Inter',sans-serif;
            color:#1e293b;background:#f8fafc;transition:all .15s;outline:none;
        }
        .input-field:focus{border-color:#4361ee;background:#fff;box-shadow:0 0 0 3px rgba(67,97,238,.1)}
        .input-field.error{border-color:#ef4444;background:#fff5f5}
        .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px}

        .submit-btn{
            width:100%;padding:14px;border:none;border-radius:10px;
            background:#4361ee;
            color:white;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;
            cursor:pointer;letter-spacing:-.1px;
            transition:background .15s,transform .1s,box-shadow .15s;
            display:flex;align-items:center;justify-content:center;gap:8px;
            box-shadow:0 4px 14px rgba(67,97,238,.35);
        }
        .submit-btn:hover{background:#3451d1;transform:translateY(-1px);box-shadow:0 6px 20px rgba(67,97,238,.45)}
        .submit-btn:active{transform:translateY(0)}

        .back-pill{
            display:inline-flex;align-items:center;gap:6px;
            border:1.5px solid rgba(255,255,255,.15);border-radius:20px;
            padding:7px 14px;color:rgba(255,255,255,.6);font-size:13px;font-weight:600;
            text-decoration:none;margin-bottom:48px;position:relative;z-index:1;
            transition:all .15s;width:fit-content;
        }
        .back-pill:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.9)}

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

        {{-- Logo mark --}}
        <div style="width:52px;height:52px;background:#4361ee;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:28px;box-shadow:0 6px 20px rgba(67,97,238,.4);font-weight:900;color:white;font-size:18px;letter-spacing:-1px">
            CV
        </div>

        <h1 style="color:white;font-size:clamp(28px,3.5vw,40px);font-weight:900;line-height:1.15;margin-bottom:14px">
            Welcome back to your<br>
            <span style="color:#4361ee">Command Center.</span>
        </h1>
        <p style="color:rgba(255,255,255,.45);font-size:15px;line-height:1.7;max-width:330px">
            Manage your events, track real-time voting, and monitor your revenue — all in one place.
        </p>

        {{-- Stats row --}}
        <div style="margin-top:36px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;max-width:340px">
            @foreach([['500+','Organizers'],['2M+','Votes Cast'],['99.9%','Uptime']] as [$v,$l])
            <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px 12px;text-align:center">
                <p style="color:white;font-size:20px;font-weight:900;line-height:1">{{ $v }}</p>
                <p style="color:rgba(255,255,255,.35);font-size:10.5px;font-weight:600;margin-top:3px">{{ $l }}</p>
            </div>
            @endforeach
        </div>

        {{-- Live event preview card --}}
        <div style="margin-top:28px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);border-radius:14px;padding:16px 18px;max-width:340px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:34px;height:34px;border-radius:9px;background:#4361ee;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div style="flex:1">
                    <p style="color:white;font-weight:700;font-size:13px;line-height:1.2">Miss Ghana 2026</p>
                    <p style="color:rgba(255,255,255,.35);font-size:11px">3 categories · 24 nominees</p>
                </div>
                <span style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.25);color:#4ade80;font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;flex-shrink:0">LIVE</span>
            </div>
            <div style="height:5px;border-radius:5px;background:rgba(255,255,255,.06);overflow:hidden;margin-bottom:10px">
                <div style="height:100%;width:68%;background:#4361ee;border-radius:5px"></div>
            </div>
            <div style="display:flex;justify-content:space-between">
                <div>
                    <p style="color:rgba(255,255,255,.35);font-size:10px;font-weight:600;text-transform:uppercase">Votes Today</p>
                    <p style="color:white;font-size:17px;font-weight:800">1,284</p>
                </div>
                <div style="text-align:right">
                    <p style="color:rgba(255,255,255,.35);font-size:10px;font-weight:600;text-transform:uppercase">Revenue</p>
                    <p style="color:#4361ee;font-size:17px;font-weight:800">GH₵ 6,420</p>
                </div>
            </div>
        </div>
    </div>

    {{-- USSD code --}}
    <div style="position:relative;z-index:1;display:flex;align-items:center;gap:10px;margin-top:24px">
        <div style="background:rgba(67,97,238,.15);border:1px solid rgba(67,97,238,.25);border-radius:10px;padding:10px 16px">
            <p style="color:rgba(255,255,255,.35);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px">USSD Code</p>
            <p style="color:white;font-size:18px;font-weight:800;letter-spacing:1.5px">*928#</p>
        </div>
        <p style="color:rgba(255,255,255,.3);font-size:12px;line-height:1.5">Works on all<br>Ghana networks</p>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right">
    <div class="form-wrap">

        {{-- Header --}}
        <div style="margin-bottom:28px">
            <h2 style="font-size:28px;font-weight:900;color:#1e293b;margin-bottom:6px">Welcome Back</h2>
            <p style="color:#94a3b8;font-size:14px">Enter your credentials to access your dashboard.</p>
        </div>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:13px 16px;font-size:13.5px;margin-bottom:20px;display:flex;align-items:flex-start;gap:8px">
            <svg style="width:15px;height:15px;flex-shrink:0;margin-top:1px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;border-radius:10px;padding:13px 16px;font-size:13.5px;margin-bottom:20px;display:flex;align-items:flex-start;gap:8px">
            <svg style="width:15px;height:15px;flex-shrink:0;margin-top:1px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            {{-- Email --}}
            <div class="field-group">
                <label class="field-label">Email Address</label>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input-field {{ $errors->has('email') ? 'error' : '' }}"
                           placeholder="you@example.com">
                </div>
            </div>

            {{-- Password --}}
            <div class="field-group" x-data="{show:false}">
                <div class="field-label-row">
                    <label class="field-label" style="margin-bottom:0">Password</label>
                    <a href="#" style="font-size:12.5px;font-weight:600;color:#4361ee;text-decoration:none">Forgot Password?</a>
                </div>
                <div class="input-icon-wrap">
                    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input :type="show ? 'text' : 'password'" name="password" required
                           class="input-field {{ $errors->has('password') ? 'error' : '' }}"
                           placeholder="••••••••••" style="padding-right:44px">
                    <button type="button" class="eye-btn" @click="show=!show">
                        <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path x-show="show" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember --}}
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:22px">
                <input type="checkbox" name="remember" id="remember"
                       style="width:15px;height:15px;accent-color:#4361ee;cursor:pointer;flex-shrink:0">
                <label for="remember" style="font-size:13px;color:#64748b;cursor:pointer;font-weight:500">Keep me signed in</label>
            </div>

            <button type="submit" class="submit-btn">
                Sign In to Dashboard
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:13.5px;color:#94a3b8">
            New organizer?
            <a href="{{ route('admin.register') }}" style="color:#4361ee;font-weight:700;text-decoration:none">Create an account →</a>
        </p>

    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
