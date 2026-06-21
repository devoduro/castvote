<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Fraud Signals — {{ $event->name }}</h2>
    <p class="text-sm text-gray-400 mb-6">Flagged patterns are shown for review. No automatic blocking occurs — admin action required.</p>

    {{-- Channel breakdown --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach($channelBreakdown as $ch)
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-400 uppercase">{{ $ch->channel }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($ch->total) }}</p>
            <p class="text-xs text-gray-400">{{ number_format($ch->transactions) }} transactions</p>
        </div>
        @endforeach
    </div>

    {{-- Burst voters --}}
    @if($burstVoters->isNotEmpty())
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
        <h3 class="font-semibold text-red-700 mb-3">🚨 Burst Voters ({{ $burstVoters->count() }}) — high transaction rate in short window</h3>
        <table class="w-full text-sm">
            <thead class="text-red-600 text-xs uppercase">
                <tr>
                    <th class="text-left py-2">Phone</th>
                    <th class="text-right py-2">Transactions</th>
                    <th class="text-right py-2">Total Votes</th>
                    <th class="text-right py-2">Span (min)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-100">
                @foreach($burstVoters as $v)
                <tr>
                    <td class="py-2 font-mono text-sm">{{ $v->voter_phone }}</td>
                    <td class="py-2 text-right font-mono">{{ $v->tx_count }}</td>
                    <td class="py-2 text-right font-mono font-bold">{{ number_format($v->total_votes) }}</td>
                    <td class="py-2 text-right font-mono">{{ $v->span_minutes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- High volume voters --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700">High Volume Voters (top 50, > 200 votes total)</h3>
        </div>
        @if($highVolume->isEmpty())
        <p class="px-6 py-8 text-center text-gray-400 text-sm">No high-volume voters detected.</p>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="text-left px-6 py-3">Phone</th>
                    <th class="text-right px-6 py-3">Total Votes</th>
                    <th class="text-right px-6 py-3">Transactions</th>
                    <th class="text-right px-6 py-3">Last Vote</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($highVolume as $v)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-sm">{{ $v->voter_phone }}</td>
                    <td class="px-6 py-3 text-right font-mono font-bold text-orange-600">{{ number_format($v->total_votes) }}</td>
                    <td class="px-6 py-3 text-right font-mono">{{ $v->transactions }}</td>
                    <td class="px-6 py-3 text-right text-xs text-gray-400">{{ \Carbon\Carbon::parse($v->last_vote)->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Hourly trend --}}
    @if($hourlyTrend->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Hourly Vote Trend (last 48 hours)</h3>
        <div class="flex items-end gap-1 h-24">
            @php $maxVotes = $hourlyTrend->max('votes') ?: 1; @endphp
            @foreach($hourlyTrend as $h)
            <div class="flex-1 flex flex-col items-center gap-1" title="{{ $h->hour }}: {{ number_format($h->votes) }} votes">
                <div class="w-full rounded-t"
                     style="height: {{ round($h->votes / $maxVotes * 80) }}px; background: #f97316; min-height: 2px;"></div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-2">Each bar = 1 hour. Peak: {{ number_format($maxVotes) }} votes/hr</p>
    </div>
    @endif
</div>
