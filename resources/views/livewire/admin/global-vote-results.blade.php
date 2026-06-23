<div>
    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#1a0030;margin-bottom:4px">Vote Results</h1>
            <p style="color:#9ca3af;font-size:13.5px">Real-time tally across all your events.</p>
        </div>
        <select wire:model.live="selectedEventId"
                style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 16px;font-size:13.5px;color:#1a0030;outline:none;background:white;font-weight:600">
            @foreach($events as $ev)
            <option value="{{ $ev->id }}">{{ $ev->name }}</option>
            @endforeach
        </select>
    </div>

    @if($selectedEvent)
    {{-- Stats row --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
        @foreach([
            ['Total Votes Cast', number_format($totalVotes), '#e91e8c', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['Revenue (GHS)',    'GHS '.number_format($totalRevenue/100,2), '#059669', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Categories',       $selectedEvent->categories->count(), '#7c3aed', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ] as [$label, $val, $color, $path])
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $color }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:22px;height:22px;color:{{ $color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                </svg>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px">{{ $label }}</p>
                <p style="font-size:22px;font-weight:800;color:#1a0030">{{ $val }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Status badge --}}
    <div style="margin-bottom:24px">
        @php
            $statusColors = ['live'=>'#059669','closed'=>'#dc2626','draft'=>'#6b7280'];
            $sc = $statusColors[$selectedEvent->status] ?? '#6b7280';
        @endphp
        <span style="background:{{ $sc }}20;color:{{ $sc }};border:1px solid {{ $sc }}40;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">
            {{ $selectedEvent->status }}
        </span>
        <span style="color:#9ca3af;font-size:13px;margin-left:8px">
            {{ $selectedEvent->starts_at?->format('d M Y') }} — {{ $selectedEvent->ends_at?->format('d M Y') }}
        </span>
    </div>

    {{-- Results by category --}}
    @forelse($results as $result)
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px;margin-bottom:20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <div>
                <h2 style="font-size:16px;font-weight:800;color:#1a0030">{{ $result['category']->name }}</h2>
                <p style="color:#9ca3af;font-size:13px">{{ number_format($result['total']) }} total votes</p>
            </div>
            <a href="{{ route('admin.events.results', $selectedEvent) }}"
               style="font-size:13px;font-weight:600;color:#e91e8c;text-decoration:none">Full results →</a>
        </div>

        @php $rank = 0; @endphp
        @forelse($result['nominees']->take(5) as $nom)
        @php
            $rank++;
            $pct = $result['total'] > 0 ? round($nom->vote_total / $result['total'] * 100, 1) : 0;
            $isFirst = $rank === 1;
        @endphp
        <div style="margin-bottom:14px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px">
                <div style="width:26px;height:26px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;{{ $isFirst ? 'background:#e91e8c;color:white' : 'background:#f3f4f6;color:#9ca3af' }}">
                    #{{ $rank }}
                </div>
                <p style="flex:1;font-weight:600;color:#1a0030;font-size:14px">{{ $nom->name }}</p>
                <p style="font-weight:700;color:#1a0030;font-size:13.5px">{{ number_format($nom->vote_total) }}</p>
                <p style="font-weight:700;color:#e91e8c;font-size:13.5px;min-width:44px;text-align:right">{{ $pct }}%</p>
            </div>
            <div style="height:8px;background:#f3f4f6;border-radius:4px;overflow:hidden">
                <div style="height:100%;border-radius:4px;background:{{ $isFirst ? 'linear-gradient(90deg,#e91e8c,#7c3aed)' : '#e5e7eb' }};width:{{ $pct }}%;transition:width .5s"></div>
            </div>
        </div>
        @empty
        <p style="color:#9ca3af;font-size:13.5px;text-align:center;padding:16px">No nominees yet</p>
        @endforelse
    </div>
    @empty
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:56px;text-align:center">
        <svg style="width:48px;height:48px;color:#e5e7eb;margin:0 auto 12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p style="color:#6b7280;font-weight:600;font-size:15px">No categories for this event yet</p>
        <a href="{{ route('admin.events.show', $selectedEvent) }}" style="display:inline-block;margin-top:12px;color:#e91e8c;font-weight:600;font-size:13.5px;text-decoration:none">Manage event →</a>
    </div>
    @endforelse

    @else
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:56px;text-align:center">
        <p style="color:#6b7280;font-weight:600;font-size:15px">No events found.</p>
        <a href="{{ route('admin.events.create') }}" style="display:inline-block;margin-top:12px;color:#e91e8c;font-weight:600;text-decoration:none">Create your first event →</a>
    </div>
    @endif
</div>
