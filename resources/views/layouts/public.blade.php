<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CastVote Ghana' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand:  { 50:'#fff7ed', 100:'#ffedd5', 200:'#fed7aa', 300:'#fdba74', 400:'#fb923c', 500:'#f97316', 600:'#ea580c', 700:'#c2410c', 800:'#9a3412', 900:'#7c2d12' },
                        navy:   { 900:'#0f172a', 800:'#1e293b', 700:'#334155', 600:'#475569' },
                    },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        .nominee-card:hover .vote-btn { background: #ea580c; }
        .animate-fade-in { animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px) } to { opacity:1; transform:none } }
    </style>
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">

{{-- ── Navigation ── --}}
<nav class="bg-navy-900 shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('vote.index') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg tracking-tight">CastVote <span class="text-brand-400">Ghana</span></span>
            </a>

            {{-- Nav links --}}
            <div class="hidden sm:flex items-center gap-6 text-sm text-slate-300">
                <a href="{{ route('vote.index') }}" class="hover:text-white transition">Events</a>
                <a href="#how-it-works" class="hover:text-white transition">How It Works</a>
                <a href="{{ route('vote.privacy') }}" class="hover:text-white transition">Privacy</a>
            </div>

            {{-- USSD badge --}}
            <div class="hidden sm:flex items-center gap-2 bg-navy-800 border border-slate-700 rounded-full px-3 py-1.5">
                <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 4.5h3M10.5 18h3"/>
                </svg>
                <span class="text-xs text-slate-300 font-medium">Dial <span class="text-brand-300 font-bold">*928#</span></span>
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak
     class="bg-emerald-50 border-b border-emerald-200 text-emerald-800 text-sm px-4 py-3 text-center font-medium">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-b border-red-200 text-red-700 text-sm px-4 py-3 text-center font-medium">
    {{ session('error') }}
</div>
@endif

{{-- Page content --}}
{{ $slot }}

{{-- ── Footer ── --}}
<footer class="bg-navy-900 text-slate-400 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-10">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-brand-500 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold">CastVote Ghana</span>
                </div>
                <p class="text-sm leading-relaxed">Ghana's trusted platform for award shows, corporate AGMs, and student elections. Secure, transparent, instant.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Vote via USSD</h4>
                <p class="text-sm leading-relaxed">No internet? No problem.<br>
                    Dial <span class="text-brand-400 font-bold text-base">*928#</span> on any Ghana network to vote from your basic phone.</p>
                <p class="text-xs mt-2 text-slate-500">MTN · Vodafone · AirtelTigo</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">Legal</h4>
                <ul class="text-sm space-y-1.5">
                    <li><a href="{{ route('vote.privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><span class="text-slate-500 text-xs">Ghana Data Protection Act 2012 (Act 843) compliant</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 pt-6 text-center text-xs text-slate-600">
            &copy; {{ date('Y') }} CastVote Ghana. All rights reserved. Payments secured by Paystack.
        </div>
    </div>
</footer>

@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
