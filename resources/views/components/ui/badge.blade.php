@props([
    'tone' => 'neutral',  // live | closed | draft | brand | gold | success | warning | neutral | onDark
    'dot'  => false,
])

@php
    $tones = [
        'live'    => 'background:#e7f8ef;color:#0c7f47',
        'closed'  => 'background:#f2f0f7;color:#6b6480',
        'draft'   => 'background:#fff5e6;color:#b54708',
        'brand'   => 'background:#ffe4ef;color:#c11062',
        'gold'    => 'background:#fff5da;color:#b54708',
        'success' => 'background:#e7f8ef;color:#0c7f47',
        'warning' => 'background:#fef3c7;color:#92400e',
        'danger'  => 'background:#fee2e2;color:#b91c1c',
        'neutral' => 'background:#f4f2f8;color:#5b5470',
        'onDark'  => 'background:rgba(255,255,255,.14);color:#fff',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge']) }} style="{{ $tones[$tone] ?? $tones['neutral'] }}">
    @if($dot)
        <span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
    @endif
    {{ $slot }}
</span>
