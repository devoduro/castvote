<x-layouts.admin>
    <x-slot name="title">Payments — {{ $event->name }}</x-slot>

    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px">
        <a href="{{ route('admin.events.index') }}" style="color:#9ca3af;text-decoration:none">Events</a>
        <svg style="width:14px;height:14px;color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.events.show', $event) }}" style="color:#9ca3af;text-decoration:none">{{ $event->name }}</a>
        <svg style="width:14px;height:14px;color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span style="color:#241038;font-weight:600">Payments</span>
    </div>

    <livewire:admin.payment-reconciliation :event="$event" />
</x-layouts.admin>
