<x-layouts.public>
<x-slot name="title">Vote Confirmed — CastVote</x-slot>

<div class="max-w-lg mx-auto px-4 py-16 sm:py-24 text-center">

    {{-- Success icon --}}
    <div class="relative inline-flex items-center justify-center mb-8">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <span class="absolute -top-1 -right-1 text-3xl">🎉</span>
    </div>

    <h1 class="text-4xl font-black text-gray-900 mb-3 tracking-tight">Vote Submitted!</h1>
    <p class="text-gray-500 leading-relaxed mb-8 max-w-sm mx-auto">
        Your payment is being processed. You'll receive an SMS confirmation once your vote is credited — usually within 2 minutes.
    </p>

    @if(session('nominee_name'))
    <div class="bg-white border-2 border-brand-200 rounded-3xl p-6 mb-8 text-left shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-xs font-bold text-green-600 uppercase tracking-wider">Vote Recorded</span>
        </div>
        <div class="space-y-3">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Voted for</p>
                <p class="text-2xl font-black text-brand-700 mt-0.5">{{ session('nominee_name') }}</p>
            </div>
            @if(session('category_name'))
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Category</p>
                <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ session('category_name') }}</p>
            </div>
            @endif
            @if(session('quantity') > 1)
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Votes</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">{{ session('quantity') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Amount</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">GHS {{ session('amount_ghs') }}</p>
                </div>
            </div>
            @endif
            @if(session('reference'))
            <div class="pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Reference</p>
                <p class="text-xs font-mono text-gray-600 mt-0.5 bg-gray-50 px-2 py-1 rounded-lg inline-block">{{ session('reference') }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('vote.event', session('event_slug', '')) }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-bold px-8 py-3.5 rounded-2xl transition shadow-lg shadow-brand-600/25 text-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            Vote Again
        </a>
        <a href="/"
           class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 font-semibold px-6 py-3.5 rounded-2xl border-2 border-gray-200 hover:border-gray-300 transition text-sm">
            All Events
        </a>
    </div>

    <p class="text-xs text-gray-400 mt-8 leading-relaxed">
        Keep your reference number for any disputes.<br>
        SMS confirmation usually arrives within 2 minutes.
    </p>
</div>

</x-layouts.public>
