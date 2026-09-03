<div>
    <div style="margin-bottom:24px">
        <h1 style="font-size:22px;font-weight:800;color:#241038;margin-bottom:4px">My Event Transactions</h1>
        <p style="color:#9ca3af;font-size:13.5px;font-style:italic">Monitor all financial activities and payment statuses for your events.</p>
    </div>

    {{-- KPI cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        @foreach([
            ['Gross Revenue',     'GHS '.number_format($grossRevenue/100,2), 'Total Collected',  '#e11d74', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Net Earnings',      'GHS '.number_format($grossRevenue*0.95/100,2), 'After Commission', '#059669', 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['Total Engagement',  number_format($totalVotes),  'Total Votes Cast',  '#6f4497', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ['Units Sold',        number_format($totalTx),     'Total Tickets',     '#d97706', 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
        ] as [$label, $val, $sub, $color, $path])
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px">
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">{{ $label }}</p>
            <p style="font-size:22px;font-weight:800;color:#241038;margin-bottom:4px">{{ $val }}</p>
            <div style="display:flex;align-items:center;gap:5px">
                <svg style="width:13px;height:13px;color:{{ $color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                </svg>
                <p style="font-size:11.5px;color:#9ca3af">{{ $sub }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters + table --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <div style="position:relative;flex:1;min-width:200px">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search on current page..."
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px 9px 36px;font-size:13.5px;outline:none;color:#241038">
            </div>
            <select wire:model.live="eventId"
                    style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:13.5px;color:#241038;outline:none;background:white">
                <option value="">All Events</option>
                @foreach($events as $ev)<option value="{{ $ev->id }}">{{ $ev->name }}</option>@endforeach
            </select>
            <select wire:model.live="statusFilter"
                    style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:13.5px;color:#241038;outline:none;background:white">
                <option value="">All Statuses</option>
                <option value="success">Success</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>
        </div>

        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Reference','Payer','Phone','Type','Amount (GHS)','Units','Status','Date'] as $h)
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                @php
                    $statusStyles = [
                        'success' => 'background:#d1fae5;color:#059669',
                        'pending' => 'background:#fef3c7;color:#d97706',
                        'failed'  => 'background:#fee2e2;color:#dc2626',
                    ];
                    $ss = $statusStyles[$payment->status] ?? 'background:#f3f4f6;color:#6b7280';
                    $channel = $payment->vote?->channel ?? 'ussd';
                @endphp
                <tr style="border-bottom:1px solid #f9fafb;transition:background .1s" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:13px 16px">
                        <p style="font-family:monospace;font-size:12px;color:#241038;font-weight:600">{{ Str::limit($payment->provider_reference, 18) }}</p>
                        <p style="font-size:11px;color:#9ca3af">{{ $payment->event->name }}</p>
                    </td>
                    <td style="padding:13px 16px;font-size:13px;color:#241038">—</td>
                    <td style="padding:13px 16px;font-size:13px;color:#241038">{{ $payment->phone_number }}</td>
                    <td style="padding:13px 16px">
                        <span style="background:#f7f5fb;color:#6f4497;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase">{{ $channel }}</span>
                    </td>
                    <td style="padding:13px 16px;font-weight:700;color:#241038;font-size:14px">{{ $payment->amountInGhs() }}</td>
                    <td style="padding:13px 16px;color:#6b7280;font-size:13.5px">{{ $payment->vote?->quantity ?? '—' }}</td>
                    <td style="padding:13px 16px">
                        <span style="{{ $ss }};padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700;text-transform:uppercase">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td style="padding:13px 16px;color:#9ca3af;font-size:12.5px;white-space:nowrap">{{ $payment->created_at->format('d M, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding:56px;text-align:center;color:#9ca3af;font-size:14px;font-weight:500">No data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
