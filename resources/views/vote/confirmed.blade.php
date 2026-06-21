<x-layouts.public>
<x-slot name="title">Vote Confirmed</x-slot>

<div class="text-center py-16">
    <div class="text-6xl mb-6">🎉</div>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Vote Submitted!</h1>
    <p class="text-gray-500 mb-6 max-w-md mx-auto">
        Your payment is being processed. You will receive an SMS confirmation once your vote is credited.
    </p>

    @if(session('nominee_name'))
    <div class="bg-brand-50 border border-brand-200 rounded-2xl p-6 max-w-sm mx-auto mb-8">
        <p class="text-sm text-gray-500">You voted for</p>
        <p class="text-2xl font-bold text-brand-700 mt-1">{{ session('nominee_name') }}</p>
        <p class="text-sm text-gray-400 mt-1">{{ session('category_name') }}</p>
        @if(session('quantity') > 1)
        <p class="text-sm text-brand-600 font-medium mt-2">{{ session('quantity') }} votes &bull; GHS {{ session('amount_ghs') }}</p>
        @endif
        @if(session('reference'))
        <p class="text-xs text-gray-400 mt-3 font-mono">Ref: {{ session('reference') }}</p>
        @endif
    </div>
    @endif

    <a href="{{ route('vote.event', session('event_slug', '')) }}"
       class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-3 rounded-xl transition">
        Vote Again
    </a>
    <p class="text-xs text-gray-400 mt-6">
        Keep your reference number for any disputes. SMS confirmation usually arrives within 2 minutes.
    </p>
</div>

</x-layouts.public>
