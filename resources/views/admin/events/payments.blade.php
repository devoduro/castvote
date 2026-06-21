<x-layouts.admin>
    <x-slot name="title">Payments — {{ $event->name }}</x-slot>
    <div class="mb-4">
        <a href="{{ route('admin.events.show', $event) }}" class="text-gray-400 hover:text-gray-600 text-sm">← Back to Event</a>
    </div>
    <livewire:admin.payment-reconciliation :event="$event" />
</x-layouts.admin>
