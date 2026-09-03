@props([
    'event',
    'href',
    'cta'    => 'View award',
    'metaLabel',            // e.g. "Cost per vote" / "Categories"
    'metaValue',
    'badge'  => null,       // optional slot-free badge text
])

{{--
    Compact, image-forward tile used by the /voting and /results pickers.
    One fact, one call to action — the browsing pages carry the richer card.
--}}
<article class="card card-hover overflow-hidden flex flex-col h-full">
    <a href="{{ $href }}" class="block relative ratio-4x3 group" tabindex="-1" aria-hidden="true">
        @if($event->flyerUrl())
            <img src="{{ $event->flyerUrl() }}" alt="" loading="lazy" decoding="async"
                 class="transition-transform duration-500 group-hover:scale-[1.04]">
        @else
            <span class="w-full h-full flex flex-col items-center justify-center gap-2 text-brand-400 px-4 text-center"
                  style="background:linear-gradient(140deg,#ffe4ef,#efeaf6)">
                <x-ui.icon name="trophy" :size="34" :stroke="1.4" />
                <span class="text-[12px] font-bold text-brand-600/70 clamp-2">{{ $event->name }}</span>
            </span>
        @endif

        @if($badge)
            <span class="absolute top-3 left-3">
                <x-ui.badge tone="live" dot>{{ $badge }}</x-ui.badge>
            </span>
        @endif
    </a>

    <div class="p-4 flex flex-col gap-2 flex-1">
        <h3 class="text-[14.5px] font-extrabold text-ink-900 leading-snug clamp-2">
            <a href="{{ $href }}" class="hover:text-brand-700 transition">{{ $event->name }}</a>
        </h3>

        @if($event->organization?->name)
            <p class="text-[12px] text-ink-400 truncate">{{ $event->organization->name }}</p>
        @endif

        <p class="text-[12.5px] text-ink-500 mt-auto">
            {{ $metaLabel }}:
            <strong class="text-ink-900 font-bold">{{ $metaValue }}</strong>
        </p>

        <x-ui.btn :href="$href" variant="primary" size="sm" block class="mt-1">{{ $cta }}</x-ui.btn>
    </div>
</article>
