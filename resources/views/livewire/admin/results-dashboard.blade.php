<div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#241038;margin-bottom:4px">{{ $event->name }} — Results</h2>
            <p style="color:#9ca3af;font-size:13px">Auto-refreshes every 2.5s &bull; GHS {{ number_format($totalRevenue / 100, 2) }} revenue</p>
        </div>
        <div style="display:flex;gap:8px">
            <button wire:click="toggleResultsVisibility"
                    style="border:1.5px solid {{ ($event->voting_rules['results_public'] ?? false) ? '#bbf7d0' : '#e5e7eb' }};background:{{ ($event->voting_rules['results_public'] ?? false) ? '#f0fdf4' : 'white' }};color:{{ ($event->voting_rules['results_public'] ?? false) ? '#059669' : '#6b7280' }};border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer">
                {{ ($event->voting_rules['results_public'] ?? false) ? '🌐 Public' : '🔒 Private' }}
            </button>
            <a href="{{ route('admin.events.export.results-pdf', $event) }}"
               style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#3c1f56,#4a2769);color:white;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:700;text-decoration:none">
                PDF
            </a>
            <a href="{{ route('admin.events.export.payments-csv', $event) }}"
               style="display:inline-flex;align-items:center;gap:6px;border:1.5px solid #e5e7eb;color:#6b7280;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none">
                CSV
            </a>
        </div>
    </div>

    {{-- Category tabs --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
        @foreach($categories as $cat)
        <button wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                style="border:none;border-radius:20px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;
                    {{ $selectedCategoryId == $cat->id
                        ? 'background:linear-gradient(135deg,#e11d74,#a00e53);color:white;box-shadow:0 4px 12px rgba(225,29,116,.35)'
                        : 'background:white;color:#6b7280;border:1.5px solid #e5e7eb' }}">
            {{ $cat->name }}
        </button>
        @endforeach
    </div>

    {{-- Results for selected category --}}
    @if($selectedResult)
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6">
            <h3 style="font-weight:700;color:#241038;font-size:15px">{{ $selectedResult['category']->name }}</h3>
            <p style="color:#9ca3af;font-size:12px;margin-top:2px">{{ number_format($selectedResult['total']) }} total votes</p>
        </div>
        <div>
            @foreach($selectedResult['nominees'] as $i => $nom)
            @php
                $pct = $selectedResult['total'] > 0 ? round($nom->vote_count / $selectedResult['total'] * 100, 1) : 0;
                $isLeader = $i === 0;
            @endphp
            <div style="padding:16px 20px;{{ !$loop->last ? 'border-bottom:1px solid #f9fafb;' : '' }}display:flex;align-items:center;gap:14px">
                <div style="width:28px;text-align:center;flex-shrink:0">
                    @if($isLeader)
                    <span style="font-size:18px">🏆</span>
                    @else
                    <span style="color:#9ca3af;font-size:13px;font-family:monospace;font-weight:600">{{ $i + 1 }}</span>
                    @endif
                </div>
                <div style="flex:1;min-width:0">
                    <p style="font-weight:{{ $isLeader ? '800' : '600' }};color:{{ $isLeader ? '#241038' : '#374151' }};font-size:14px;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $nom->name }}
                    </p>
                    <div style="height:8px;background:#f3f4f6;border-radius:20px;overflow:hidden">
                        <div style="height:100%;border-radius:20px;transition:width .5s ease;width:{{ $pct }}%;
                            {{ $isLeader ? 'background:linear-gradient(90deg,#e11d74,#f472b6)' : 'background:#c4b5fd' }}"></div>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;width:90px">
                    <p style="font-family:monospace;font-weight:800;color:#241038;font-size:16px">{{ number_format($nom->vote_count) }}</p>
                    <p style="font-size:12px;color:#9ca3af">{{ $pct }}%</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:60px;text-align:center">
        <p style="color:#9ca3af;font-size:14px">Select a category to view results.</p>
    </div>
    @endif
</div>
