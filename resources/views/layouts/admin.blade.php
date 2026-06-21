<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — CastVote</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#fff7ed', 100:'#ffedd5', 500:'#f97316', 600:'#ea580c', 700:'#c2410c', 900:'#7c2d12' }
                    }
                }
            }
        }
    </script>
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen font-sans">

{{-- Top Nav --}}
<nav class="bg-brand-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight">
                    CastVote <span class="text-brand-100 text-sm font-normal">Admin</span>
                </a>
                @auth('admin')
                <a href="{{ route('admin.events.index') }}" class="text-brand-100 hover:text-white text-sm">Events</a>
                @endauth
            </div>
            @auth('admin')
            <div class="flex items-center gap-4">
                <span class="text-brand-200 text-sm">
                    {{ auth('admin')->user()->name }}
                    <span class="ml-1 px-2 py-0.5 bg-brand-900 rounded text-xs uppercase">{{ auth('admin')->user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="text-sm text-brand-200 hover:text-white">Sign out</button>
                </form>
            </div>
            @endauth
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 text-sm">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 text-sm">
    {{ session('error') }}
</div>
@endif

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{ $slot }}
</main>

@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
