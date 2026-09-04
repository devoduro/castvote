@props([
    'size'  => 34,
    'light' => false,   // white wordmark for dark backgrounds
    'mark'  => false,   // icon only
    'href'  => null,
])

@php $href = $href ?? url('/'); @endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 shrink-0']) }}
   aria-label="ClickVote — home">
    <span style="width:{{ $size }}px;height:{{ $size }}px;border-radius:{{ round($size / 3) }}px;
                 background:linear-gradient(135deg,#e11d74,#6f4497);
                 display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="{{ round($size * .55) }}" height="{{ round($size * .55) }}" viewBox="0 0 24 24"
             fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </span>
    @unless($mark)
        <span class="font-display font-extrabold tracking-tight {{ $light ? 'text-white' : 'text-ink-950' }}"
              style="font-size:{{ round($size * .53) }}px">Click<span class="text-brand-600">Vote</span></span>
    @endunless
</a>
