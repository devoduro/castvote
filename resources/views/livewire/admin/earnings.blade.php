<div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#1a0030;margin-bottom:4px">Earnings &amp; Payouts</h1>
            <p style="color:#9ca3af;font-size:13.5px">Track your revenue and financial history.</p>
        </div>
        <select wire:model.live="eventFilter"
                style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 16px;font-size:13.5px;color:#1a0030;outline:none;background:white">
            <option value="">All Events</option>
            @foreach($events as $ev)<option value="{{ $ev->id }}">{{ $ev->name }}</option>@endforeach
        </select>
    </div>

    <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;margin-bottom:24px">
        {{-- Withdrawable balance card --}}
        <div style="background:linear-gradient(135deg,#2d0050,#3b0068);border-radius:20px;padding:28px 32px;position:relative;overflow:hidden">
            <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;background:rgba(255,255,255,.04);border-radius:50%"></div>
            <div style="position:absolute;bottom:-30px;right:80px;width:80px;height:80px;background:rgba(255,255,255,.03);border-radius:50%"></div>
            <p style="color:rgba(255,255,255,.5);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px">Withdrawable Balance</p>
            <p style="color:white;font-size:36px;font-weight:900;margin-bottom:24px;position:relative">
                GHS {{ number_format($netRevenue/100,2) }}
            </p>
            <div style="display:flex;align-items:center;gap:12px;position:relative">
                <button style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:white;border-radius:10px;padding:10px 20px;font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px">
                    Request Payout
                    <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </button>
                <div style="background:rgba(255,255,255,.1);border-radius:10px;padding:10px 16px">
                    <p style="color:rgba(255,255,255,.5);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em">Total Withdrawn</p>
                    <p style="color:white;font-weight:800;font-size:15px;margin-top:1px">GHS 0.00</p>
                </div>
            </div>
        </div>

        {{-- Right stats --}}
        <div style="display:flex;flex-direction:column;gap:12px">
            <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                    <div style="width:32px;height:32px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center">
                        <svg style="width:16px;height:16px;color:#059669" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">Total Verified Revenue</p>
                </div>
                <p style="font-size:20px;font-weight:800;color:#1a0030">GHS {{ number_format($grossRevenue/100,2) }}</p>
            </div>
            <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px">
                <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Organizer Net Share</p>
                <p style="font-size:26px;font-weight:900;color:#1a0030">GHS {{ number_format($netRevenue/100,2) }}</p>
                <div style="display:flex;align-items:center;gap:6px;margin-top:8px">
                    <div style="width:8px;height:8px;background:#059669;border-radius:50%"></div>
                    <p style="font-size:12px;color:#6b7280">Verified Earnings (After 5% Commission)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Per-event breakdown --}}
    @if($eventBreakdown->count())
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin-bottom:24px">
        <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6">
            <h3 style="font-size:15px;font-weight:700;color:#1a0030">Revenue by Event</h3>
        </div>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Event','Transactions','Gross (GHS)','Net (GHS)','Status'] as $h)
                    <th style="padding:11px 20px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($eventBreakdown as $row)
                @php $statusC = $row->event->status === 'live' ? '#059669' : ($row->event->status === 'closed' ? '#6b7280' : '#d97706'); @endphp
                <tr style="border-bottom:1px solid #f9fafb">
                    <td style="padding:13px 20px;font-weight:600;color:#1a0030">{{ $row->event->name }}</td>
                    <td style="padding:13px 20px;color:#6b7280">{{ number_format($row->tx_count) }}</td>
                    <td style="padding:13px 20px;font-weight:700;color:#1a0030">{{ number_format($row->total/100,2) }}</td>
                    <td style="padding:13px 20px;font-weight:700;color:#059669">{{ number_format($row->total*0.95/100,2) }}</td>
                    <td style="padding:13px 20px">
                        <span style="background:{{ $statusC }}20;color:{{ $statusC }};padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700;text-transform:uppercase">{{ $row->event->status }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Payout history --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:#1a0030">Payout History</h3>
                <p style="color:#9ca3af;font-size:13px;margin-top:2px">Status of your withdrawal requests.</p>
            </div>
            <div style="position:relative">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search reference..." style="border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 14px 8px 34px;font-size:13px;outline:none;color:#1a0030">
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa">
                    @foreach(['Reference','Event','Payment Details','Date','Amount','Status'] as $h)
                    <th style="padding:11px 20px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" style="padding:56px;text-align:center">
                        <svg style="width:40px;height:40px;color:#e5e7eb;margin:0 auto 12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p style="color:#9ca3af;font-weight:600;font-size:13px;text-transform:uppercase;letter-spacing:.05em">No Payout Records Found.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
