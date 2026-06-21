<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Payments — {{ $event->name }}</h2>

    {{-- Summary chips --}}
    <div class="flex flex-wrap gap-3 mb-4">
        @foreach(['success' => 'green', 'pending' => 'amber', 'failed' => 'red', 'reversed' => 'gray'] as $s => $color)
        @if(isset($summary[$s]))
        <button wire:click="$set('statusFilter', '{{ $s }}')"
                class="px-3 py-1.5 rounded-full text-xs font-semibold
                    {{ $statusFilter === $s ? 'ring-2 ring-offset-1 ring-'.$color.'-400' : '' }}
                    bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
            {{ strtoupper($s) }}: {{ number_format($summary[$s]->count) }}
            (GHS {{ number_format($summary[$s]->total / 100, 2) }})
        </button>
        @endif
        @endforeach
        @if($statusFilter)
        <button wire:click="$set('statusFilter', '')" class="text-xs text-gray-400 hover:text-gray-600 underline">Clear filter</button>
        @endif
    </div>

    {{-- Search --}}
    <div class="mb-3">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search phone or reference…"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 focus:ring-2 focus:ring-orange-500 outline-none">
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Reference</th>
                    <th class="text-left px-4 py-3">Phone</th>
                    <th class="text-left px-4 py-3">Network</th>
                    <th class="text-right px-4 py-3">Amount</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Created</th>
                    <th class="text-center px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $p->provider_reference }}</td>
                    <td class="px-4 py-3">{{ $p->phone_number }}</td>
                    <td class="px-4 py-3 uppercase text-xs">{{ $p->momo_network ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-mono font-medium">GHS {{ $p->amountInGhs() }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $p->status === 'success' ? 'bg-green-100 text-green-700' : ($p->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                            {{ strtoupper($p->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $p->created_at->diffForHumans() }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($p->status === 'pending')
                        <button wire:click="reverify({{ $p->id }})"
                                wire:loading.attr="disabled"
                                class="text-xs text-orange-600 hover:underline font-medium">Re-verify</button>
                        @else
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
    </div>
</div>
