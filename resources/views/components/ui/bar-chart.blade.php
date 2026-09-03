@props([
    'series',              // iterable of ['label' => string, 'value' => int|float, 'caption' => ?string]
    'height'  => 140,
    'color'   => '#e11d74',
    'format'  => null,     // optional closure to format a value for the tooltip
])

@php
    $rows = collect($series);
    $max  = max(1, (int) $rows->max('value'));
    $fmt  = $format ?? fn ($v) => number_format($v);
@endphp

@if($rows->isEmpty())
    <p class="text-[13px] text-ink-400 py-8 text-center">No activity in this period yet.</p>
@else
    <div {{ $attributes->merge(['class' => 'flex items-end gap-[3px] sm:gap-1.5']) }}
         style="height:{{ $height }}px" role="img"
         aria-label="Bar chart: {{ $rows->map(fn ($r) => $r['label'] . ' ' . $fmt($r['value']))->implode(', ') }}">
        @foreach($rows as $row)
            @php $pct = $max > 0 ? max(2, round($row['value'] / $max * 100)) : 2; @endphp
            <div class="flex-1 h-full flex flex-col justify-end group relative min-w-0">
                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 rounded-lg
                             bg-ink-950 text-white text-[11px] font-semibold whitespace-nowrap opacity-0
                             group-hover:opacity-100 transition pointer-events-none z-10">
                    {{ $row['label'] }} · {{ $fmt($row['value']) }}
                </span>
                <span class="block rounded-t-[4px] transition-all"
                      style="height:{{ $pct }}%;background:{{ $row['value'] > 0 ? $color : '#ece9f3' }};opacity:{{ $row['value'] > 0 ? 1 : .6 }}"></span>
            </div>
        @endforeach
    </div>

    <div class="flex justify-between mt-2 text-[10.5px] text-ink-400 font-medium">
        <span>{{ $rows->first()['caption'] ?? $rows->first()['label'] }}</span>
        <span>{{ $rows->last()['caption'] ?? $rows->last()['label'] }}</span>
    </div>
@endif
