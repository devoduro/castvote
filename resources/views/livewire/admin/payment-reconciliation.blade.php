<div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#1a0030;margin-bottom:4px">Payments — {{ $event->name }}</h2>
            <p style="color:#9ca3af;font-size:13px">All payment transactions for this event.</p>
        </div>
    </div>

    {{-- Summary chips --}}
    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px">
        @php $chipCfg = ['success'=>['#d1fae5','#059669','#bbf7d0'],'pending'=>['#fef3c7','#d97706','#fde68a'],'failed'=>['#fee2e2','#dc2626','#fecaca'],'reversed'=>['#f3f4f6','#6b7280','#e5e7eb']]; @endphp
        @foreach($chipCfg as $s => [$bg, $tc, $border])
        @if(isset($summary[$s]))
        <button wire:click="$set('statusFilter', '{{ $s }}')"
                style="background:{{ $bg }};color:{{ $tc }};border:1.5px solid {{ $statusFilter === $s ? $tc : $border }};border-radius:20px;padding:7px 14px;font-size:12px;font-weight:700;cursor:pointer;{{ $statusFilter === $s ? 'box-shadow:0 0 0 3px '.$bg : '' }}">
            {{ strtoupper($s) }}: {{ number_format($summary[$s]->count) }}
            (GHS {{ number_format($summary[$s]->total / 100, 2) }})
        </button>
        @endif
        @endforeach
        @if($statusFilter)
        <button wire:click="$set('statusFilter', '')"
                style="background:none;border:none;color:#9ca3af;font-size:13px;cursor:pointer;text-decoration:underline">
            Clear filter
        </button>
        @endif
    </div>

    {{-- Search --}}
    <div style="position:relative;display:inline-block;margin-bottom:16px">
        <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search phone or reference…"
               style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px 9px 36px;font-size:13.5px;outline:none;color:#1a0030;width:280px">
    </div>

    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Reference','Phone','Network','Amount','Status','Time','Action'] as $h)
                    <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                @php
                    $sc=['success'=>['#d1fae5','#059669'],'pending'=>['#fef3c7','#d97706'],'failed'=>['#fee2e2','#dc2626'],'reversed'=>['#f3f4f6','#6b7280']];
                    [$sbg,$stc]=$sc[$p->status]??['#f3f4f6','#6b7280'];
                @endphp
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:13px 16px;font-family:monospace;font-size:12px;color:#6b7280">{{ $p->provider_reference }}</td>
                    <td style="padding:13px 16px;font-size:13.5px;color:#1a0030;font-weight:600">{{ $p->phone_number }}</td>
                    <td style="padding:13px 16px">
                        <span style="background:#f3f0ff;color:#7c3aed;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase">
                            {{ $p->momo_network ?? '—' }}
                        </span>
                    </td>
                    <td style="padding:13px 16px;font-family:monospace;font-weight:700;color:#059669;font-size:14px">GHS {{ $p->amountInGhs() }}</td>
                    <td style="padding:13px 16px">
                        <span style="background:{{ $sbg }};color:{{ $stc }};padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700;text-transform:uppercase">
                            {{ strtoupper($p->status) }}
                        </span>
                    </td>
                    <td style="padding:13px 16px;font-size:12px;color:#9ca3af;white-space:nowrap">{{ $p->created_at->diffForHumans() }}</td>
                    <td style="padding:13px 16px">
                        @if($p->status === 'pending')
                        <button wire:click="reverify({{ $p->id }})" wire:loading.attr="disabled"
                                style="font-size:12.5px;font-weight:700;color:#e91e8c;background:none;border:none;cursor:pointer;text-decoration:none">
                            Re-verify
                        </button>
                        @else
                        <span style="color:#d1d5db">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:56px;text-align:center;color:#9ca3af;font-size:14px">No payments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($payments->hasPages())
        <div style="padding:16px;border-top:1px solid #f3f4f6">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
