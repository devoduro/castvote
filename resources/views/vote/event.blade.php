<x-layouts.public>
<x-slot name="title">Vote — {{ $event->name }}</x-slot>

{{-- Event hero --}}
<div class="text-center mb-8">
    <span class="text-xs font-semibold px-2 py-1 rounded-full
        {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
        {{ strtoupper($event->event_type) }}
    </span>
    <h1 class="text-3xl font-bold text-gray-900 mt-3">{{ $event->name }}</h1>
    <p class="text-gray-400 text-sm mt-1">
        Voting closes <strong>{{ $event->ends_at?->format('d M Y, g:ia') }}</strong>
        @if($event->isPayPerVote())
        &bull; <span class="text-brand-600 font-medium">GHS {{ $event->priceInGhs() }} per vote</span>
        @endif
    </p>
</div>

{{-- Eligibility gate for elections/AGMs --}}
@if($event->requiresEligibilityList())
    <livewire:vote.eligibility-gate :event="$event" />
@else
    <livewire:vote.ballot :event="$event" />
@endif

</x-layouts.public>
