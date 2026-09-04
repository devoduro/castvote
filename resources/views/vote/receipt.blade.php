<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ClickVote receipt {{ $payment->provider_reference }}</title>
    <style>
        @page { margin: 22px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #241038; font-size: 11px; margin: 0; }
        .head { border-bottom: 2px solid #e11d74; padding-bottom: 12px; margin-bottom: 16px; }
        .brand { font-size: 20px; font-weight: bold; color: #14031f; }
        .brand span { color: #e11d74; }
        .muted { color: #8b849c; }
        h1 { font-size: 14px; margin: 16px 0 10px; color: #14031f; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 7px 0; border-bottom: 1px solid #ece9f3; vertical-align: top; }
        td.k { color: #6b6480; width: 42%; }
        td.v { font-weight: bold; text-align: right; }
        .total { background: #fff1f7; padding: 12px; margin-top: 14px; }
        .total .amt { font-size: 18px; font-weight: bold; color: #c11062; }
        .status { display: inline-block; padding: 3px 9px; font-size: 9px; font-weight: bold; }
        .ok { background: #e7f8ef; color: #0c7f47; }
        .pending { background: #fef3c7; color: #92400e; }
        .failed { background: #fee2e2; color: #b91c1c; }
        .ref { font-family: DejaVu Sans Mono, monospace; font-size: 9.5px; word-break: break-all; }
        .foot { margin-top: 22px; padding-top: 10px; border-top: 1px solid #ece9f3; font-size: 9px; color: #8b849c; line-height: 1.6; }
    </style>
</head>
<body>

@php
    $meta     = $payment->metadata ?? [];
    $quantity = (int) ($meta['quantity'] ?? 1);
    $phone    = $payment->phone_number;
    // Mask the middle of the payer's number — the receipt only needs to confirm which line paid.
    $masked   = $phone ? substr($phone, 0, 3) . str_repeat('*', max(0, strlen($phone) - 6)) . substr($phone, -3) : '—';
    $statusClass = ['success' => 'ok', 'pending' => 'pending'][$payment->status] ?? 'failed';
@endphp

<div class="head">
    <div class="brand">Click<span>Vote</span></div>
    <div class="muted">Vote receipt &bull; {{ $payment->created_at->format('d M Y, g:ia') }}</div>
</div>

<span class="status {{ $statusClass }}">{{ strtoupper($payment->status) }}</span>

<h1>Transaction</h1>
<table>
    @if($nominee)
        <tr><td class="k">Nominee</td><td class="v">{{ $nominee->name }} ({{ $nominee->code }})</td></tr>
    @endif
    @if($nominee?->category)
        <tr><td class="k">Category</td><td class="v">{{ $nominee->category->name }}</td></tr>
    @endif
    @if($payment->event)
        <tr><td class="k">Award / campaign</td><td class="v">{{ $payment->event->name }}</td></tr>
    @endif
    @if($payment->event?->organization)
        <tr><td class="k">Organiser</td><td class="v">{{ $payment->event->organization->name }}</td></tr>
    @endif
    <tr><td class="k">Number of votes</td><td class="v">{{ number_format($quantity) }}</td></tr>
    <tr><td class="k">Payment method</td><td class="v">{{ $payment->momo_network ? ucfirst($payment->momo_network) . ' Mobile Money' : 'Paystack' }}</td></tr>
    <tr><td class="k">Paying number</td><td class="v">{{ $masked }}</td></tr>
    <tr><td class="k">Channel</td><td class="v">{{ strtoupper($meta['channel'] ?? 'web') }}</td></tr>
    <tr><td class="k">Date &amp; time</td><td class="v">{{ $payment->created_at->format('d M Y, g:ia') }}</td></tr>
    <tr><td class="k">Reference</td><td class="v ref">{{ $payment->provider_reference }}</td></tr>
</table>

<div class="total">
    <table>
        <tr style="border:0">
            <td class="k" style="border:0;padding:0">Total paid</td>
            <td class="v amt" style="border:0;padding:0">GHS {{ $payment->amountInGhs() }}</td>
        </tr>
    </table>
</div>

<div class="foot">
    Payments are processed by Paystack in Ghana Cedis. Votes are final and cannot be reversed.<br>
    Keep this reference for any dispute; contact the campaign organiser if a confirmed payment was not credited.<br>
    &copy; {{ date('Y') }} ClickVote Ghana &bull; Compliant with the Data Protection Act, 2012 (Act 843)
</div>

</body>
</html>
