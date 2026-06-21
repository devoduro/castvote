<x-layouts.admin>
    <x-slot name="title">{{ isset($event) && $event->exists ? 'Edit Event' : 'New Event' }}</x-slot>
    <livewire:admin.event-form :event="$event ?? null" />
</x-layouts.admin>
