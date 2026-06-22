<x-layouts.public>
<x-slot name="title">Vote — {{ $event->name }}</x-slot>

{{-- Event Hero --}}
@if($event->flyerUrl())
<div class="relative">
    <img src="{{ $event->flyerUrl() }}" alt="{{ $event->name }}"
         class="w-full h-64 sm:h-[28rem] object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
    <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10">
        <div class="max-w-6xl mx-auto">
            <span class="text-xs font-bold px-3 py-1.5 rounded-full mb-3 inline-block shadow
                {{ $event->event_type === 'award' ? 'bg-purple-600 text-white' : ($event->event_type === 'election' ? 'bg-blue-600 text-white' : 'bg-teal-600 text-white') }}">
                {{ strtoupper($event->event_type) }}
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-white mt-2 leading-tight drop-shadow-lg">{{ $event->name }}</h1>
            <div class="flex flex-wrap items-center gap-3 mt-3 text-sm">
                <span class="flex items-center gap-1.5 text-gray-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Closes {{ $event->ends_at?->format('d M Y, g:ia') ?? 'TBD' }}
                </span>
                @if($event->isPayPerVote())
                <span class="font-semibold text-brand-300">GHS {{ $event->priceInGhs() }} per vote</span>
                @else
                <span class="font-semibold text-green-400">✓ Free to vote</span>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="bg-gradient-to-br from-gray-950 via-brand-900 to-gray-900 text-white py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="/" class="inline-flex items-center gap-1.5 text-gray-400 hover:text-white text-sm mb-5 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            All Events
        </a>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full mb-3 inline-block
            {{ $event->event_type === 'award' ? 'bg-purple-600 text-white' : ($event->event_type === 'election' ? 'bg-blue-600 text-white' : 'bg-teal-600 text-white') }}">
            {{ strtoupper($event->event_type) }}
        </span>
        <h1 class="text-3xl sm:text-4xl font-black mt-2 leading-tight">{{ $event->name }}</h1>
        <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-gray-300">
            <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Closes {{ $event->ends_at?->format('d M Y, g:ia') ?? 'TBD' }}
            </span>
            @if($event->isPayPerVote())
            <span class="font-semibold text-brand-300">GHS {{ $event->priceInGhs() }} per vote</span>
            @else
            <span class="font-semibold text-green-400">✓ Free to vote</span>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Back link for flyer hero variant --}}
@if($event->flyerUrl())
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    <a href="/" class="inline-flex items-center gap-1.5 text-gray-400 hover:text-gray-700 text-sm transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        All Events
    </a>
</div>
@endif

{{-- Ballot --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-900">Select a Category</h2>
        <p class="text-gray-500 text-sm mt-1">Choose a category below to view nominees and cast your vote.</p>
    </div>

    @if($event->requiresEligibilityList())
        <livewire:vote.eligibility-gate :event="$event" />
    @else
        <livewire:vote.ballot :event="$event" />
    @endif
</div>

</x-layouts.public>
