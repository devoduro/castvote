<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vote') — CastVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#fff7ed', 100:'#ffedd5', 200:'#fed7aa', 500:'#f97316', 600:'#ea580c', 700:'#c2410c', 900:'#7c2d12' }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen font-sans">

{{-- Nav --}}
<header class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="text-xl font-bold text-brand-700">CastVote <span class="text-gray-400 font-normal text-sm">Ghana</span></a>
        <span class="text-xs text-gray-400">Secure · Transparent · Instant</span>
    </div>
</header>

{{-- Flash --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     class="bg-green-50 border-b border-green-200 text-green-800 text-sm px-4 py-3 text-center">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-b border-red-200 text-red-700 text-sm px-4 py-3 text-center">
    {{ session('error') }}
</div>
@endif

<main class="max-w-4xl mx-auto px-4 py-8">
    {{ $slot }}
</main>

<footer class="border-t border-gray-100 mt-16 py-8 text-center text-xs text-gray-400">
    <p>CastVote &copy; {{ date('Y') }} — Powered by CastVote Ghana</p>
    <p class="mt-1">
        <a href="/privacy" class="hover:underline">Privacy Policy</a> &bull;
        Compliant with Ghana Data Protection Act 2012 (Act 843)
    </p>
</footer>

@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
