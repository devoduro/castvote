<x-layouts.public>
<x-slot name="title">Vote Smart, Vote Secure — CastVote Ghana</x-slot>

{{-- ═══════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════ --}}
<section class="relative bg-gray-950 text-white overflow-hidden">
    {{-- Background pattern --}}
    <div class="absolute inset-0 opacity-10"
         style="background-image:radial-gradient(circle at 20% 50%, #ea580c 0%, transparent 50%), radial-gradient(circle at 80% 20%, #c2410c 0%, transparent 50%);">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-center">
        <div class="inline-flex items-center gap-2 bg-brand-600/20 border border-brand-500/40 text-brand-300 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
            {{ $liveCount }} Live Event{{ $liveCount !== 1 ? 's' : '' }} Now
        </div>
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-none tracking-tight mb-6">
            Vote Smart,<br>
            <span class="text-brand-500">Vote Secure!</span>
        </h1>
        <p class="text-gray-300 text-xl max-w-2xl mx-auto leading-relaxed mb-10">
            Transform your events with effortless e-voting. Make every vote count with confidence — powered by Mobile Money.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#events"
               class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-black text-lg px-8 py-4 rounded-2xl transition shadow-xl shadow-brand-900/50">
                Vote Now
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <a href="#how-it-works"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-lg px-8 py-4 rounded-2xl transition">
                How It Works
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </a>
        </div>

        {{-- Trust badges --}}
        <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-500">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Secured by Paystack
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                MTN · Telecel · AirtelTigo MoMo
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                Ghana DPA Compliant
            </span>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     VOTING EVENTS
════════════════════════════════════════════════════════ --}}
<section id="events" class="py-16 sm:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-brand-600 text-sm font-bold uppercase tracking-widest mb-1">Current Events</p>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900">Voting Events</h2>
            </div>
            @if($events->count() > 6)
            <a href="#events" class="hidden sm:inline-flex items-center gap-1.5 text-brand-600 hover:text-brand-700 font-bold text-sm transition">
                View All
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            @endif
        </div>

        @forelse($events as $event)
        @php $isLive = $event->status === 'live'; @endphp
        <a href="{{ $isLive ? route('vote.event', $event->slug) : '#' }}"
           class="group flex flex-col sm:flex-row bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 mb-5 overflow-hidden {{ !$isLive ? 'opacity-75 cursor-default' : '' }}">

            {{-- Image --}}
            <div class="relative sm:w-64 sm:shrink-0 overflow-hidden bg-gray-200">
                @if($event->flyerUrl())
                <img src="{{ $event->flyerUrl() }}" alt="{{ $event->name }}"
                     class="w-full h-52 sm:h-full object-cover {{ $isLive ? 'group-hover:scale-105' : '' }} transition-transform duration-500">
                @else
                <div class="w-full h-52 sm:h-full bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                @endif
                {{-- Status badge --}}
                <div class="absolute top-3 left-3">
                    @if($isLive)
                    <span class="inline-flex items-center gap-1.5 bg-green-500 text-white text-xs font-black px-2.5 py-1 rounded-full shadow">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Live
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-600 text-white text-xs font-black px-2.5 py-1 rounded-full shadow">
                        Ended
                    </span>
                    @endif
                </div>
                {{-- Price badge --}}
                @if($event->isPayPerVote())
                <div class="absolute top-3 right-3">
                    <span class="bg-brand-600 text-white text-xs font-black px-2.5 py-1 rounded-full shadow">
                        GHS {{ $event->priceInGhs() }}/vote
                    </span>
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col justify-between p-5 flex-1 min-w-0">
                <div>
                    <span class="inline-block text-xs font-black uppercase tracking-wider px-2.5 py-1 rounded-md mb-3
                        {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : ($event->event_type === 'election' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                        {{ $event->event_type === 'award' ? 'VOTING' : strtoupper($event->event_type) }}
                    </span>
                    <h3 class="text-xl font-black text-gray-900 {{ $isLive ? 'group-hover:text-brand-700' : '' }} transition leading-tight mb-3">
                        {{ $event->name }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $event->ends_at?->format('D, d M Y') ?? 'TBD' }}
                        </span>
                        @if(!$event->isPayPerVote())
                        <span class="flex items-center gap-1 text-green-600 font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Free to vote
                        </span>
                        @endif
                    </div>
                </div>
                @if($isLive)
                <div class="mt-4">
                    <span class="inline-flex items-center gap-2 bg-brand-600 group-hover:bg-brand-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition">
                        Vote Now
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </span>
                </div>
                @endif
            </div>
        </a>
        @empty
        <div class="text-center py-24 bg-white rounded-2xl border border-gray-100">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">No live events right now</h2>
            <p class="text-gray-400">Check back soon or contact the event organiser.</p>
        </div>
        @endforelse
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     HOW IT WORKS
════════════════════════════════════════════════════════ --}}
<section id="how-it-works" class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-brand-600 text-sm font-bold uppercase tracking-widest mb-1">Simple &amp; Fast</p>
            <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-3">How It Works</h2>
            <p class="text-gray-500 max-w-lg mx-auto">Launch your event and start earning in three simple steps.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Step 1 --}}
            <div class="text-center group">
                <div class="w-16 h-16 bg-brand-50 group-hover:bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300">
                    <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="text-brand-600 font-black text-4xl mb-1">01</div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Create</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Set up your event in minutes. Customise categories, nominees, and voting rules from your dashboard.</p>
            </div>

            {{-- Step 2 --}}
            <div class="text-center group">
                <div class="w-16 h-16 bg-brand-50 group-hover:bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300">
                    <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <div class="text-brand-600 font-black text-4xl mb-1">02</div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Share</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Share your unique voting link via WhatsApp and social media to engage your audience instantly.</p>
            </div>

            {{-- Step 3 --}}
            <div class="text-center group">
                <div class="w-16 h-16 bg-brand-50 group-hover:bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-5 transition-all duration-300">
                    <svg class="w-8 h-8 text-brand-600 group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-brand-600 font-black text-4xl mb-1">03</div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Receive Payouts</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Track revenue in real-time. Receive payouts directly to your Mobile Money wallet or Bank Account.</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     STATS
════════════════════════════════════════════════════════ --}}
<section class="py-16 bg-gray-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm font-bold uppercase tracking-widest mb-10">Platform Impact — Trusted by organizers for scale, speed, and security</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center bg-white/5 rounded-2xl p-6 border border-white/10">
                <p class="text-4xl font-black text-brand-500 mb-1">{{ number_format($totalVotes) }}+</p>
                <p class="text-sm text-gray-400">Votes Processed</p>
            </div>
            <div class="text-center bg-white/5 rounded-2xl p-6 border border-white/10">
                <p class="text-4xl font-black text-brand-500 mb-1">{{ $totalEvents }}+</p>
                <p class="text-sm text-gray-400">Events Hosted</p>
            </div>
            <div class="text-center bg-white/5 rounded-2xl p-6 border border-white/10">
                <p class="text-4xl font-black text-green-400 mb-1">{{ $liveCount }}</p>
                <p class="text-sm text-gray-400">Live Now</p>
            </div>
            <div class="text-center bg-white/5 rounded-2xl p-6 border border-white/10">
                <p class="text-4xl font-black text-brand-500 mb-1">100%</p>
                <p class="text-sm text-gray-400">Secure &amp; Compliant</p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CTA BANNER
════════════════════════════════════════════════════════ --}}
<section class="py-16 bg-brand-600">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Don't Miss the Next Big Event</h2>
        <p class="text-brand-100 mb-8 leading-relaxed">Join thousands of Ghanaians voting online. Select an event above and cast your vote in seconds.</p>
        <a href="#events"
           class="inline-flex items-center gap-2 bg-white hover:bg-brand-50 text-brand-700 font-black text-lg px-8 py-4 rounded-2xl transition shadow-lg">
            Browse Events
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </div>
</section>

</x-layouts.public>
