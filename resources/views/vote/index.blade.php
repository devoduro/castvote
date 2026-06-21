<x-layouts.public>
<x-slot name="title">Live Voting Events</x-slot>

<div class="text-center mb-10">
    <h1 class="text-3xl font-bold text-gray-900">Live Voting Events</h1>
    <p class="text-gray-500 mt-2">Select an event below to cast your vote</p>
</div>

@forelse($events as $event)
<a href="{{ route('vote.event', $event->slug) }}"
   class="block bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 hover:border-brand-200 transition p-6 mb-4 group">
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full mr-2
                {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : ($event->event_type === 'election' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                {{ strtoupper($event->event_type) }}
            </span>
            <h2 class="text-xl font-bold text-gray-800 mt-2 group-hover:text-brand-700 transition">{{ $event->name }}</h2>
            <p class="text-sm text-gray-400 mt-1">
                Voting closes {{ $event->ends_at?->format('d M Y, g:ia') ?? 'TBD' }}
            </p>
            @if($event->isPayPerVote())
            <p class="text-sm text-brand-600 font-medium mt-1">GHS {{ $event->priceInGhs() }} per vote</p>
            @else
            <p class="text-sm text-green-600 font-medium mt-1">Free to vote</p>
            @endif
        </div>
        <div class="text-brand-500 group-hover:translate-x-1 transition-transform text-2xl">→</div>
    </div>
</a>
@empty
<div class="text-center py-20 text-gray-400">
    <p class="text-4xl mb-4">🗳️</p>
    <p class="text-lg font-medium">No live events right now</p>
    <p class="text-sm mt-1">Check back soon or contact the event organiser</p>
</div>
@endforelse

</x-layouts.public>
