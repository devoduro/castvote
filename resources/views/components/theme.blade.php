{{--
    CastVote design system — single source of truth for tokens, typography and
    component classes. Included from the <head> of every layout so the public
    site and the admin console share one visual language.
--}}
<meta name="theme-color" content="#e11d74">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans:    ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'sans-serif'],
                },
                colors: {
                    /* Primary — CastVote magenta. Replaces the stray orange scale so
                       every existing `brand-*` class becomes on-brand automatically. */
                    brand: {
                        50:'#fff1f7', 100:'#ffe4ef', 200:'#fecce0', 300:'#fda4c7', 400:'#fa6da5',
                        500:'#f03d86', 600:'#e11d74', 700:'#c11062', 800:'#a00e53', 900:'#85104a', 950:'#500426',
                    },
                    /* Ink — deep aubergine used for headings, nav and dark sections. */
                    ink: {
                        50:'#f7f5fb', 100:'#efeaf6', 200:'#ded4ed', 300:'#c3b1dd', 400:'#a184c8',
                        500:'#855fb2', 600:'#6f4497', 700:'#5b357b', 800:'#3c1f56', 900:'#241038', 950:'#14031f',
                    },
                    /* Gold — award accent. Used sparingly: 1st place, winners, badges. */
                    gold: {
                        50:'#fffaeb', 100:'#fef0c7', 200:'#fedf89', 300:'#fec84b',
                        400:'#fdb022', 500:'#f79009', 600:'#dc6803', 700:'#b54708',
                    },
                },
                boxShadow: {
                    card:  '0 1px 2px rgba(20,3,31,.04), 0 4px 16px rgba(20,3,31,.06)',
                    lift:  '0 8px 28px rgba(20,3,31,.12)',
                    brand: '0 8px 24px rgba(225,29,116,.28)',
                },
                borderRadius: { '4xl': '1.75rem' },
                maxWidth: { site: '1180px' },
            },
        },
    }
</script>
<style>
    *,*::before,*::after{box-sizing:border-box}
    :root{
        --cv-brand:#e11d74; --cv-brand-dark:#c11062; --cv-ink:#14031f;
        --cv-radius:14px; --cv-ring:0 0 0 3px rgba(225,29,116,.28);
    }
    html{-webkit-text-size-adjust:100%}
    body{margin:0;font-family:'Inter',ui-sans-serif,system-ui,sans-serif;-webkit-font-smoothing:antialiased;color:#241038}
    h1,h2,h3,h4{font-family:'Plus Jakarta Sans','Inter',sans-serif;letter-spacing:-.02em}
    a{color:inherit;text-decoration:none}
    img{max-width:100%;height:auto}
    [x-cloak]{display:none!important}

    /* Visible, consistent focus for keyboard users across the whole app. */
    :where(a,button,input,select,textarea,[tabindex]):focus-visible{
        outline:none;box-shadow:var(--cv-ring);border-radius:10px;
    }
    .skip-link{position:absolute;left:-9999px;top:0;z-index:100;background:#fff;color:var(--cv-brand);
        padding:10px 18px;border-radius:0 0 12px 0;font-weight:700;font-size:14px}
    .skip-link:focus{left:0}

    /* ── Button system ───────────────────────────────────────────────
       One height, one radius, one motion curve. Variants only change colour. */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;
        height:44px;padding:0 20px;border-radius:12px;border:1.5px solid transparent;
        font-size:14.5px;font-weight:700;line-height:1;cursor:pointer;white-space:nowrap;
        transition:background .15s,border-color .15s,color .15s,box-shadow .15s,transform .1s;
        font-family:'Inter',sans-serif}
    .btn:active:not(:disabled){transform:translateY(1px)}
    .btn:disabled,.btn[aria-disabled="true"]{opacity:.55;cursor:not-allowed;transform:none}
    .btn-sm{height:36px;padding:0 14px;font-size:13px;border-radius:10px}
    .btn-lg{height:52px;padding:0 28px;font-size:16px;border-radius:14px}
    .btn-block{width:100%}

    .btn-primary{background:var(--cv-brand);color:#fff;box-shadow:0 6px 18px rgba(225,29,116,.26)}
    .btn-primary:hover:not(:disabled){background:var(--cv-brand-dark)}
    .btn-secondary{background:var(--cv-ink);color:#fff}
    .btn-secondary:hover:not(:disabled){background:#3c1f56}
    .btn-outline{background:#fff;color:var(--cv-ink);border-color:#e2e0ea}
    .btn-outline:hover:not(:disabled){border-color:var(--cv-brand);color:var(--cv-brand);background:#fff1f7}
    .btn-ghost{background:transparent;color:#6b6480}
    .btn-ghost:hover:not(:disabled){background:#f4f2f8;color:var(--cv-ink)}
    .btn-success{background:#0f9d58;color:#fff}
    .btn-success:hover:not(:disabled){background:#0c7f47}
    .btn-danger{background:#dc2626;color:#fff}
    .btn-danger:hover:not(:disabled){background:#b91c1c}
    .btn-onDark{background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.28)}
    .btn-onDark:hover:not(:disabled){background:rgba(255,255,255,.2)}

    /* Spinner shown by [wire:loading] or an .is-loading class. */
    .btn-spin{width:16px;height:16px;border:2px solid rgba(255,255,255,.4);
        border-top-color:#fff;border-radius:50%;animation:cv-spin .6s linear infinite}
    .btn-outline .btn-spin,.btn-ghost .btn-spin{border-color:rgba(20,3,31,.2);border-top-color:var(--cv-brand)}
    @keyframes cv-spin{to{transform:rotate(360deg)}}
    @keyframes cv-pulse{0%,100%{opacity:1}50%{opacity:.45}}
    @keyframes cv-shimmer{100%{background-position:200% 0}}
    @keyframes cv-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
    .cv-in{animation:cv-in .35s ease both}
    .live-dot{animation:cv-pulse 1.6s ease-in-out infinite}

    /* ── Surfaces ── */
    .card{background:#fff;border:1px solid #ece9f3;border-radius:18px;
        box-shadow:0 1px 2px rgba(20,3,31,.04),0 4px 16px rgba(20,3,31,.05)}
    .card-hover{transition:box-shadow .2s,transform .2s,border-color .2s}
    .card-hover:hover{box-shadow:0 12px 32px rgba(20,3,31,.13);transform:translateY(-3px);border-color:#e2dcef}

    /* ── Form controls ── */
    .label{display:block;font-size:13.5px;font-weight:700;color:#3c1f56;margin-bottom:7px}
    .hint{font-size:12.5px;color:#8b849c;margin-top:6px;line-height:1.55}
    .input{width:100%;height:46px;padding:0 14px;font-size:15px;color:#241038;background:#fff;
        border:1.5px solid #e2e0ea;border-radius:12px;outline:none;font-family:inherit;
        transition:border-color .15s,box-shadow .15s}
    textarea.input{height:auto;padding:12px 14px;line-height:1.6;resize:vertical}
    select.input{appearance:none;padding-right:38px;cursor:pointer;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%238b849c' stroke-width='2'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
        background-repeat:no-repeat;background-position:right 13px center}
    .input::placeholder{color:#a9a3b8}
    .input:focus{border-color:var(--cv-brand);box-shadow:var(--cv-ring)}
    .input[aria-invalid="true"],.input.is-error{border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.15)}
    .error-msg{display:flex;align-items:center;gap:5px;font-size:12.5px;font-weight:600;color:#dc2626;margin-top:6px}

    /* ── Badges ── */
    .badge{display:inline-flex;align-items:center;gap:5px;height:24px;padding:0 10px;
        border-radius:999px;font-size:11.5px;font-weight:700;letter-spacing:.02em;white-space:nowrap}

    /* ── Layout helpers ── */
    .site{max-width:1180px;margin:0 auto;padding-left:20px;padding-right:20px}
    /* Grid tracks are min-content sized by default, so a single nowrap button
       or long word can push a row wider than the phone. Let items shrink. */
    .grid > *{min-width:0}
    .eyebrow{font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--cv-brand)}
    .clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .skeleton{background:linear-gradient(90deg,#f1eff6 25%,#e7e3f0 37%,#f1eff6 63%);
        background-size:400% 100%;animation:cv-shimmer 1.4s ease infinite;border-radius:10px}

    /* Media placeholders keep a fixed ratio so cards never jump while images load. */
    .ratio-16x9{aspect-ratio:16/9;background:#f4f1fa;overflow:hidden}
    .ratio-4x3{aspect-ratio:4/3;background:#f4f1fa;overflow:hidden}
    .ratio-1x1{aspect-ratio:1/1;background:#f4f1fa;overflow:hidden}
    .ratio-16x9>img,.ratio-4x3>img,.ratio-1x1>img{width:100%;height:100%;object-fit:cover;display:block}

    /* Tap targets stay finger-sized on phones. */
    @media (max-width:640px){
        .btn{height:46px}
        .btn-sm{height:40px}
        .site{padding-left:16px;padding-right:16px}
    }
    @media (prefers-reduced-motion:reduce){
        *,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;
            transition-duration:.01ms!important;scroll-behavior:auto!important}
    }
</style>
