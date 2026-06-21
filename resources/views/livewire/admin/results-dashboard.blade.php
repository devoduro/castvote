<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $event->name }} — Results</h2>
            <p class="text-sm text-gray-400 mt-0.5">Auto-refreshes every 2.5 seconds &bull; GHS {{ number_format($totalRevenue / 100, 2) }} revenue</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="toggleResultsVisibility"
                    class="text-sm px-3 py-1.5 rounded-lg border {{ ($event->voting_rules['results_public'] ?? false) ? 'border-green-300 text-green-700 bg-green-50' : 'border-gray-300 text-gray-600' }}">
                {{ ($event->voting_rules['results_public'] ?? false) ? 'Public' : 'Private' }}
            </button>
            <a href="{{ route('admin.events.export.results-pdf', $event) }}"
               class="text-sm px-3 py-1.5 rounded-lg bg-orange-600 text-white hover:bg-orange-700 font-medium">PDF</a>
            <a href="{{ route('admin.events.export.payments-csv', $event) }}"
               class="text-sm px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">CSV</a>
        </div>
    </div>

    {{-- Category tabs --}}
    <div class="flex gap-2 flex-wrap mb-4">
        @foreach($categories as $cat)
        <button wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                class="text-sm px-3 py-1.5 rounded-full font-medium transition
                    {{ $selectedCategoryId == $cat->id ? 'bg-orange-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            {{ $cat->name }}
        </button>
        @endforeach
    </div>

    {{-- Results for selected category --}}
    @if($selectedResult)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700">{{ $selectedResult['category']->name }}</h3>
            <p class="text-xs text-gray-400">{{ number_format($selectedResult['total']) }} total votes</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($selectedResult['nominees'] as $i => $nom)
            @php
                $pct = $selectedResult['total'] > 0 ? round($nom->vote_count / $selectedResult['total'] * 100, 1) : 0;
            @endphp
            <div class="px-6 py-4 flex items-center gap-4">
                <div class="w-7 text-center">
                    @if($i === 0)
                        <span class="text-amber-500 font-bold">🏆</span>
                    @else
                        <span class="text-gray-400 text-sm font-mono">{{ $i + 1 }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-800 truncate {{ $i === 0 ? 'text-orange-700' : '' }}">{{ $nom->name }}</div>
                    <div class="mt-1.5 h-2 bg-gray-100 rounded-full overflow-hidden w-full">
                        <div class="h-full rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-orange-500' : 'bg-gray-300' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                <div class="text-right w-28 shrink-0">
                    <div class="font-mono font-bold text-gray-800">{{ number_format($nom->vote_count) }}</div>
                    <div class="text-xs text-gray-400">{{ $pct }}%</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
