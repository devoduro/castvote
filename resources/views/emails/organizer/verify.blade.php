<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Your Email</title>
<style>
  body { margin:0; padding:0; background:#f5f5f5; font-family:'Helvetica Neue',Arial,sans-serif; }
  .wrapper { max-width:560px; margin:40px auto; }
  .card { background:white; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
  .logo-bar { padding:28px 40px 0; text-align:center; }
  .logo-bar img { height:40px; }
  .logo-text { font-size:22px; font-weight:800; color:#1a0030; letter-spacing:-.5px; }
  .logo-text span { color:#e91e8c; }
  .body { padding:36px 40px 32px; }
  h1 { font-size:26px; font-weight:800; color:#1a0030; margin:0 0 20px; }
  p { font-size:15px; color:#374151; line-height:1.65; margin:0 0 16px; }
  .btn-wrap { text-align:center; margin:32px 0; }
  .btn { display:inline-block; background:#2d0050; color:#ffffff !important; text-decoration:none;
         font-size:15px; font-weight:700; padding:14px 36px; border-radius:10px;
         letter-spacing:.2px; }
  .fallback { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px 20px; margin-top:24px; }
  .fallback p { font-size:13px; color:#6b7280; margin:0 0 8px; }
  .fallback a { color:#2d0050; font-size:13px; word-break:break-all; }
  .footer { padding:20px 40px 28px; text-align:center; font-size:12px; color:#9ca3af; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo-bar">
      <p class="logo-text">Cast<span>Vote</span></p>
    </div>
    <div class="body">
      <h1>Verify Your Email</h1>
      <p>Hi {{ $admin->name }},</p>
      <p>Welcome to <strong>{{ config('app.name') }}</strong>. To complete your registration and secure your account, please verify your email address by clicking the button below:</p>

      <div class="btn-wrap">
        <a href="{{ $verifyUrl }}" class="btn">Verify Email Address</a>
      </div>

      <p style="font-size:13.5px;color:#6b7280">This link expires in <strong>24 hours</strong>. After verifying, your account will be reviewed by our team and you'll be notified once approved.</p>

      <div class="fallback">
        <p>If you're having trouble with the button, copy and paste this link:</p>
        <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a>
      </div>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }}. If you didn't create an account, you can safely ignore this email.
    </div>
  </div>
</div>
</body>
</html>
