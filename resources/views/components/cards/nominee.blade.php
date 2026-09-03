@props([
    'nominee',
    'event'      => null,
    'showVotes'  => false,   // only ever true when the organiser published results
    'votes'      => null,
    'rank'       => null,
])

@php
    $event    = $event ?? $nominee->category?->event;
    $category = $nominee->category;
    $canVote  = $event && $event->status === 'live' && $event->isLive();
    $href     = route('nominees.show', $nominee);
@endphp

<article class="card card-hover overflow-hidden flex flex-col h-full">

    <a href="{{ $href }}" class="block relative ratio-4x3 group" tabindex="-1" aria-hidden="true">
        @if($nominee->photo_path)
            <img src="{{ asset('storage/' . $nominee->photo_path) }}" alt="" loading="lazy" decoding="async"
                 class="transition-transform duration-500 group-hover:scale-[1.05]">
        @else
            <span class="w-full h-full flex items-center justify-center font-display font-extrabold text-brand-400"
                  style="background:linear-gradient(135deg,#ffe4ef,#efeaf6);font-size:44px">
                {{ Str::upper(Str::substr($nominee->name, 0, 1)) }}
            </span>
        @endif

        @if($rank)
            <span class="absolute top-3 left-3 w-8 h-8 rounded-full flex items-center justify-center
                         text-[13px] font-extrabold text-white shadow-lift"
                  style="background:{{ $rank === 1 ? '#dc6803' : ($rank === 2 ? '#6b6480' : ($rank === 3 ? '#a16207' : '#e11d74')) }}">
                {{ $rank }}
            </span>
        @endif

        <span class="absolute bottom-3 left-3 badge"
              style="background:rgba(20,3,31,.72);color:#fff;backdrop-filter:blur(6px);font-family:ui-monospace,monospace">
            {{ $nominee->code }}
        </span>
    </a>

    <div class="p-4 sm:p-5 flex flex-col gap-2 flex-1">
        <h3 class="text-[16px] font-extrabold text-ink-900 leading-snug clamp-2">
            <a href="{{ $href }}" class="hover:text-brand-700 transition">{{ $nominee->name }}</a>
        </h3>

        @if($category)
            <p class="text-[13px] text-ink-500 clamp-2">{{ $category->name }}</p>
        @endif

        @if($showVotes)
            <p class="text-[13px] font-bold text-ink-700 flex items-center gap-1.5">
                <x-ui.icon name="chart" :size="14" class="text-brand-500" />
                {{ number_format($votes ?? $nominee->totalVotes()) }} votes
            </p>
        @endif

        <div class="mt-auto pt-3">
            @if($canVote)
                <x-ui.btn :href="$href . '#vote'" variant="primary" size="sm" block icon-end="arrow-right">
                    Vote Now
                </x-ui.btn>
            @else
                <x-ui.btn :href="$href" variant="outline" size="sm" block>View Profile</x-ui.btn>
            @endif
        </div>
    </div>
</article>
