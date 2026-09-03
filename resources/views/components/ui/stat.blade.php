@props([
    'label',
    'value',
    'sub'   => null,
    'icon'  => null,
    'tone'  => 'brand',   // brand | ink | success | gold | violet
    'solid' => false,     // filled card (dashboards) vs light card
])

@php
    $tones = [
        'brand'   => ['bg' => '#e11d74', 'soft' => '#ffe4ef', 'fg' => '#c11062'],
        'ink'     => ['bg' => '#3c1f56', 'soft' => '#efeaf6', 'fg' => '#3c1f56'],
        'success' => ['bg' => '#0f9d58', 'soft' => '#e7f8ef', 'fg' => '#0c7f47'],
        'gold'    => ['bg' => '#dc6803', 'soft' => '#fff5da', 'fg' => '#b54708'],
        'violet'  => ['bg' => '#6f4497', 'soft' => '#f1eaf9', 'fg' => '#5b357b'],
    ];
    $t = $tones[$tone] ?? $tones['brand'];
@endphp

@if($solid)
    <div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl p-5 sm:p-6']) }}
         style="background:{{ $t['bg'] }}">
        @if($icon)
            <div class="absolute -bottom-3 -right-2 opacity-20 pointer-events-none text-white">
                <x-ui.icon :name="$icon" :size="88" :stroke="1.4" />
            </div>
        @endif
        <div class="relative">
            <p class="text-white font-extrabold leading-none" style="font-size:clamp(24px,3vw,32px)">{{ $value }}</p>
            <p class="text-white/90 text-[13px] font-bold mt-2">{{ $label }}</p>
            @if($sub)<p class="text-white/60 text-[11.5px] font-medium mt-1">{{ $sub }}</p>@endif
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'card p-5']) }}>
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[12px] font-bold uppercase tracking-wider text-ink-400">{{ $label }}</p>
                <p class="text-2xl sm:text-[28px] font-extrabold text-ink-900 leading-tight mt-1.5">{{ $value }}</p>
                @if($sub)<p class="text-[12.5px] text-ink-500 mt-1">{{ $sub }}</p>@endif
            </div>
            @if($icon)
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:{{ $t['soft'] }};color:{{ $t['fg'] }}">
                    <x-ui.icon :name="$icon" :size="19" />
                </div>
            @endif
        </div>
    </div>
@endif
