<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — CastVote Organizer Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;display:flex;min-height:100vh}

        /* Left panel */
        .left{
            width:52%;background:linear-gradient(160deg,#3b0068 0%,#2d0050 40%,#1a0030 100%);
            padding:32px 48px;display:flex;flex-direction:column;position:relative;overflow:hidden;
        }
        /* Dotted background pattern */
        .left::before{
            content:'';position:absolute;inset:0;
            background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);
            background-size:22px 22px;pointer-events:none;
        }

        /* Right panel */
        .right{
            flex:1;background:white;display:flex;align-items:center;justify-content:center;padding:48px;
        }
        .form-wrap{width:100%;max-width:400px}

        /* Input group with icon */
        .field-group{margin-bottom:18px}
        .field-label{font-size:13px;font-weight:600;color:#1a0030;margin-bottom:6px;display:block}
        .field-label-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
        .input-icon-wrap{position:relative}
        .input-icon-wrap .icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            width:16px;height:16px;color:#9ca3af;pointer-events:none;
        }
        .input-field{
            width:100%;border:1.5px solid #e5e7eb;border-radius:10px;
            padding:12px 14px 12px 42px;font-size:14px;font-family:'Inter',sans-serif;
            color:#1a0030;background:#f0eef8;transition:all .15s;outline:none;
        }
        .input-field:focus{border-color:#e91e8c;background:#fff;box-shadow:0 0 0 3px rgba(233,30,140,.1)}
        .input-field.error{border-color:#ef4444;background:#fff5f5}

        .eye-btn{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px}

        /* Submit btn */
        .submit-btn{
            width:100%;padding:14px;border:none;border-radius:10px;
            background:linear-gradient(135deg,#2d0050,#3b0068);
            color:white;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;
            cursor:pointer;letter-spacing:-.1px;
            transition:opacity .15s,transform .1s;display:flex;align-items:center;justify-content:center;gap:8px;
        }
        .submit-btn:hover{opacity:.92;transform:translateY(-1px)}
        .submit-btn:active{transform:translateY(0)}

        /* Back to home pill */
        .back-pill{
            display:inline-flex;align-items:center;gap:6px;
            border:1.5px solid rgba(233,30,140,.5);border-radius:20px;
            padding:7px 14px;color:#e91e8c;font-size:13px;font-weight:600;
            text-decoration:none;margin-bottom:48px;position:relative;z-index:1;
            transition:all .15s;width:fit-content;
        }
        .back-pill:hover{background:rgba(233,30,140,.1)}

        /* Social proof card */
        .social-card{
            border-radius:14px;padding:16px 18px;
            background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
            display:flex;align-items:center;gap:14px;position:relative;z-index:1;
        }

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
        {{-- Icon --}}
        <div style="width:56px;height:56px;background:rgba(255,255,255,.12);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:28px">
            <svg style="width:28px;height:28px;color:#e91e8c" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>

        <h1 style="color:white;font-size:38px;font-weight:900;line-height:1.15;margin-bottom:16px">
            Welcome back to your<br>
            <span style="color:#e91e8c">Command Center.</span>
        </h1>
        <p style="color:rgba(255,255,255,.55);font-size:15px;line-height:1.65;max-width:340px">
            Log in to manage your events, monitor real-time voting revenue, and download ticketing reports.
        </p>

        {{-- Social proof --}}
        <div class="social-card" style="margin-top:36px;max-width:340px">
            <div style="display:flex">
                @foreach(['#e91e8c','#7c3aed','#ea580c'] as $c)
                <div style="width:32px;height:32px;border-radius:50%;background:{{ $c }};border:2px solid rgba(255,255,255,.3);margin-left:-6px;display:flex;align-items:center;justify-content:center;font-size:11px;color:white;font-weight:700">
                    {{ chr(65 + $loop->index) }}
                </div>
                @endforeach
            </div>
            <p style="color:rgba(255,255,255,.75);font-size:13.5px;font-weight:600">
                Trusted by 500+ top organizers
            </p>
        </div>
    </div>

    {{-- USSD pill --}}
    <div style="position:relative;z-index:1;display:flex;align-items:center;gap:10px;margin-top:24px">
        <div style="background:rgba(233,30,140,.15);border:1px solid rgba(233,30,140,.3);border-radius:10px;padding:10px 16px">
            <p style="color:rgba(255,255,255,.4);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px">USSD Code</p>
            <p style="color:white;font-size:18px;font-weight:800;letter-spacing:1.5px">*928#</p>
        </div>
        <p style="color:rgba(255,255,255,.35);font-size:12px;line-height:1.5">Works on all<br>Ghana networks</p>
    </div>
</div>

{{-- ── RIGHT PANEL ── --}}
<div class="right">
    <div class="form-wrap">
        <h2 style="font-size:30px;font-weight:900;color:#1a0030;margin-bottom:6px">Welcome Back</h2>
        <p style="color:#9ca3af;font-size:14px;margin-bottom:28px">Enter your credentials to access your dashboard.</p>

        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:10px;padding:13px 16px;font-size:13.5px;margin-bottom:20px">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #fecaca;color:#dc2626;border-radius:10px;padding:13px 16px;font-size:13.5px;margin-bottom:20px">
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
                    <a href="#" style="font-size:12.5px;font-weight:600;color:#e91e8c;text-decoration:none">Forgot Password?</a>
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

            <button type="submit" class="submit-btn" style="margin-top:6px">
                Access Dashboard
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:13.5px;color:#9ca3af">
            New to CastVote?
            <a href="{{ route('admin.register') }}" style="color:#e91e8c;font-weight:700;text-decoration:none">Create Organizer Account</a>
        </p>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
