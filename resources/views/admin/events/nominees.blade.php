<x-layouts.admin>
    <x-slot name="title">Nominees — {{ $category->name }}</x-slot>
    <div class="mb-4 flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.events.show', $event) }}" class="hover:text-gray-600">← {{ $event->name }}</a>
    </div>
    <livewire:admin.nominee-manager :event="$event" :category="$category" />
</x-layouts.admin>
