<x-layouts.admin>
    <x-slot name="title">Results — {{ $event->name }}</x-slot>
    <div class="mb-4">
        <a href="{{ route('admin.events.show', $event) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Back to Event</a>
    </div>
    <livewire:admin.results-dashboard :event="$event" />
</x-layouts.admin>
