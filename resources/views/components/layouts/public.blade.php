<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Vote' }} — CastVote Ghana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50:'#fff7ed', 100:'#ffedd5', 200:'#fed7aa', 300:'#fdba74', 500:'#f97316', 600:'#ea580c', 700:'#c2410c', 800:'#9a3412', 900:'#7c2d12' }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

{{-- Top Nav --}}
<header x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0">
                <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-lg font-extrabold text-gray-900 tracking-tight">CastVote <span class="text-brand-600">Ghana</span></span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="/" class="text-gray-600 hover:text-brand-600 px-3 py-2 rounded-lg hover:bg-brand-50 transition">Home</a>
                <a href="/#events" class="text-gray-600 hover:text-brand-600 px-3 py-2 rounded-lg hover:bg-brand-50 transition">Voting</a>
                <a href="/#how-it-works" class="text-gray-600 hover:text-brand-600 px-3 py-2 rounded-lg hover:bg-brand-50 transition">How It Works</a>
                <a href="/privacy" class="text-gray-600 hover:text-brand-600 px-3 py-2 rounded-lg hover:bg-brand-50 transition">Privacy</a>
            </nav>

            {{-- CTA + mobile menu toggle --}}
            <div class="flex items-center gap-2">
                <a href="/#events"
                   class="hidden sm:inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                    Vote Now
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <button @click="open = !open" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                    <svg x-show="!open" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open" x-transition class="md:hidden border-t border-gray-100 py-3 space-y-1">
            <a href="/" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition">Home</a>
            <a href="/#events" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition">Voting</a>
            <a href="/#how-it-works" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition">How It Works</a>
            <a href="/privacy" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition">Privacy</a>
            <a href="/#events" class="block mt-2 bg-brand-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl text-center transition">Vote Now →</a>
        </div>
    </div>
</header>

{{-- Flash messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     class="bg-green-600 text-white text-sm px-4 py-3 text-center font-medium flex items-center justify-center gap-2">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-600 text-white text-sm px-4 py-3 text-center font-medium flex items-center justify-center gap-2">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    {{ session('error') }}
</div>
@endif

<main>
    {{ $slot }}
</main>

{{-- Rich 4-col Footer --}}
<footer class="bg-gray-950 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">

            {{-- Col 1: Brand --}}
            <div>
                <a href="/" class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-white font-extrabold text-lg">CastVote <span class="text-brand-500">Ghana</span></span>
                </a>
                <p class="text-sm leading-relaxed mb-5">
                    Ghana's reliable e-voting platform. Create events, manage votes, and receive payouts securely via Mobile Money.
                </p>
                {{-- Social --}}
                <div class="flex items-center gap-3">
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand-600 rounded-lg flex items-center justify-center transition" aria-label="Facebook">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand-600 rounded-lg flex items-center justify-center transition" aria-label="X / Twitter">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand-600 rounded-lg flex items-center justify-center transition" aria-label="Instagram">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
                    </a>
                </div>
            </div>

            {{-- Col 2: Quick Links --}}
            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-5">Quick Links</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/" class="hover:text-white hover:translate-x-1 transition-all inline-block">Home</a></li>
                    <li><a href="/#events" class="hover:text-white hover:translate-x-1 transition-all inline-block">Vote Now</a></li>
                    <li><a href="/#how-it-works" class="hover:text-white hover:translate-x-1 transition-all inline-block">How It Works</a></li>
                    <li><a href="/privacy" class="hover:text-white hover:translate-x-1 transition-all inline-block">Privacy Policy</a></li>
                </ul>
            </div>

            {{-- Col 3: Contact Info --}}
            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-5">Contact Info</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-brand-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Accra, Ghana</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-brand-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+233 244 000 001</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-brand-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>info@castvote.test</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-brand-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mon–Fri 8AM – 5PM<br><span class="text-xs text-gray-500">24/7 Technical Support</span></span>
                    </li>
                </ul>
            </div>

            {{-- Col 4: We Accept --}}
            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-5">We Accept</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-gray-800 rounded-lg px-3 py-2 text-center">
                        <p class="text-yellow-400 font-black text-xs">MTN</p>
                        <p class="text-gray-400 text-xs">MoMo</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-3 py-2 text-center">
                        <p class="text-red-400 font-black text-xs">Telecel</p>
                        <p class="text-gray-400 text-xs">Cash</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-3 py-2 text-center">
                        <p class="text-blue-400 font-black text-xs">AirtelTigo</p>
                        <p class="text-gray-400 text-xs">Money</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-3 py-2 text-center">
                        <p class="text-green-400 font-black text-xs">Paystack</p>
                        <p class="text-gray-400 text-xs">Secured</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-3 leading-relaxed">Compliant with Ghana Data Protection Act 2012 (Act 843)</p>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6 text-center text-xs text-gray-600">
            &copy; {{ date('Y') }} CastVote Ghana. All rights reserved.
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
