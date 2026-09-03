<div>
    <div style="margin-bottom:24px">
        <h2 style="font-size:20px;font-weight:800;color:#241038;margin-bottom:4px">Fraud Signals — {{ $event->name }}</h2>
        <p style="color:#9ca3af;font-size:13px">Flagged patterns are shown for review. No automatic blocking — admin action required.</p>
    </div>

    {{-- Channel breakdown --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px">
        @foreach($channelBreakdown as $ch)
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:18px;position:relative;overflow:hidden">
            <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#e11d74,#6f4497)"></div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">{{ $ch->channel }}</p>
            <p style="font-size:26px;font-weight:900;color:#241038">{{ number_format($ch->total) }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:2px">{{ number_format($ch->transactions) }} transactions</p>
        </div>
        @endforeach
    </div>

    {{-- Burst voters alert --}}
    @if($burstVoters->isNotEmpty())
    <div style="background:#fff5f5;border:1.5px solid #fecaca;border-radius:16px;padding:20px;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
            <div style="width:32px;height:32px;background:#fee2e2;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px">🚨</div>
            <h3 style="font-weight:700;color:#dc2626;font-size:14px">Burst Voters ({{ $burstVoters->count() }}) — high transaction rate in short window</h3>
        </div>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="border-bottom:1px solid #fecaca">
                    @foreach(['Phone','Transactions','Total Votes','Span (min)'] as $h)
                    <th style="padding:8px 0;text-align:{{ $h==='Phone'?'left':'right' }};font-size:11px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($burstVoters as $v)
                <tr style="border-bottom:1px solid #fef2f2">
                    <td style="padding:10px 0;font-family:monospace;font-size:13.5px;color:#241038">{{ $v->voter_phone }}</td>
                    <td style="padding:10px 0;text-align:right;font-family:monospace;color:#374151">{{ $v->tx_count }}</td>
                    <td style="padding:10px 0;text-align:right;font-family:monospace;font-weight:800;color:#dc2626;font-size:15px">{{ number_format($v->total_votes) }}</td>
                    <td style="padding:10px 0;text-align:right;font-family:monospace;color:#9ca3af">{{ $v->span_minutes }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- High volume voters --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin-bottom:24px">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6">
            <h3 style="font-weight:700;color:#241038;font-size:14px">High Volume Voters <span style="color:#9ca3af;font-weight:400;font-size:13px">(top 50, &gt; 200 votes)</span></h3>
        </div>
        @if($highVolume->isEmpty())
        <div style="padding:48px;text-align:center">
            <div style="width:40px;height:40px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:20px">✅</div>
            <p style="color:#9ca3af;font-size:14px">No high-volume voters detected.</p>
        </div>
        @else
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Phone','Total Votes','Transactions','Last Vote'] as $h)
                    <th style="padding:11px 20px;text-align:{{ $h==='Phone'?'left':'right' }};font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($highVolume as $v)
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:12px 20px;font-family:monospace;font-size:13.5px;color:#241038;font-weight:600">{{ $v->voter_phone }}</td>
                    <td style="padding:12px 20px;text-align:right;font-family:monospace;font-weight:800;color:#e11d74;font-size:15px">{{ number_format($v->total_votes) }}</td>
                    <td style="padding:12px 20px;text-align:right;font-family:monospace;color:#374151">{{ $v->transactions }}</td>
                    <td style="padding:12px 20px;text-align:right;font-size:12px;color:#9ca3af">{{ \Carbon\Carbon::parse($v->last_vote)->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Hourly trend bar chart --}}
    @if($hourlyTrend->isNotEmpty())
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px">
        <h3 style="font-weight:700;color:#241038;font-size:14px;margin-bottom:16px">Hourly Vote Trend <span style="color:#9ca3af;font-weight:400;font-size:13px">(last 48 hours)</span></h3>
        @php $maxV = $hourlyTrend->max('votes') ?: 1; @endphp
        <div style="display:flex;align-items:flex-end;gap:3px;height:80px">
            @foreach($hourlyTrend as $h)
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px" title="{{ $h->hour }}: {{ number_format($h->votes) }} votes">
                <div style="width:100%;border-radius:4px 4px 0 0;background:linear-gradient(180deg,#e11d74,#a00e53);min-height:2px;height:{{ round($h->votes / $maxV * 72) }}px;transition:height .3s"></div>
            </div>
            @endforeach
        </div>
        <p style="font-size:12px;color:#9ca3af;margin-top:8px">Each bar = 1 hour. Peak: {{ number_format($maxV) }} votes/hr</p>
    </div>
    @endif
</div>
