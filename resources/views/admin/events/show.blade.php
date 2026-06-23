<x-layouts.admin>
    <x-slot name="title">{{ $event->name }}</x-slot>

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px">
        <a href="{{ route('admin.events.index') }}" style="color:#9ca3af;text-decoration:none;font-weight:500">Events</a>
        <svg style="width:14px;height:14px;color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span style="color:#1a0030;font-weight:600">{{ $event->name }}</span>
    </div>

    {{-- Event header card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px;margin-bottom:24px;display:flex;align-items:center;gap:20px">
        @if($event->flyer_path)
        <img src="{{ asset('storage/'.$event->flyer_path) }}" style="width:80px;height:80px;border-radius:14px;object-fit:cover;flex-shrink:0">
        @else
        <div style="width:80px;height:80px;border-radius:14px;background:linear-gradient(135deg,#e91e8c,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg style="width:36px;height:36px" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif
        <div style="flex:1">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <h1 style="font-size:20px;font-weight:800;color:#1a0030">{{ $event->name }}</h1>
                @php $sc=['live'=>['#d1fae5','#059669'],'draft'=>['#f3f4f6','#6b7280'],'closed'=>['#fee2e2','#dc2626']]; [$bg,$tc]=$sc[$event->status]??['#f3f4f6','#6b7280']; @endphp
                <span style="background:{{ $bg }};color:{{ $tc }};padding:3px 12px;border-radius:20px;font-size:11.5px;font-weight:700;text-transform:uppercase">{{ ucfirst($event->status) }}</span>
            </div>
            <p style="color:#9ca3af;font-size:13.5px">
                {{ $event->starts_at?->format('d M Y H:i') ?? '—' }} → {{ $event->ends_at?->format('d M Y H:i') ?? '—' }}
            </p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('admin.events.edit', $event) }}"
               style="display:inline-flex;align-items:center;gap:6px;border:1.5px solid #e5e7eb;color:#6b7280;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none">
                Edit Event
            </a>
            <a href="{{ route('admin.events.results', $event) }}"
               style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:700;text-decoration:none">
                View Results
            </a>
        </div>
    </div>

    {{-- Quick nav tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap">
        <a href="{{ route('admin.events.results', $event) }}"
           style="display:inline-flex;align-items:center;gap:6px;background:white;border:1.5px solid #e5e7eb;color:#059669;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none">
            📊 Results
        </a>
        <a href="{{ route('admin.events.payments', $event) }}"
           style="display:inline-flex;align-items:center;gap:6px;background:white;border:1.5px solid #e5e7eb;color:#d97706;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none">
            💳 Payments
        </a>
        <a href="{{ route('admin.events.fraud', $event) }}"
           style="display:inline-flex;align-items:center;gap:6px;background:white;border:1.5px solid #e5e7eb;color:#dc2626;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none">
            🚨 Fraud Panel
        </a>
    </div>

    {{-- Category manager --}}
    <livewire:admin.category-manager :event="$event" />
</x-layouts.admin>
