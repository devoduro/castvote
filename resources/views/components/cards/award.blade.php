@props(['event'])

@php
    $isLive    = $event->status === 'live' && $event->isLive();
    $isClosed  = ! $isLive;
    $cats      = $event->categories_count ?? $event->categories()->count();
    $organiser = $event->organization?->name;
    $href      = route('awards.show', $event->slug);
@endphp

<article class="card card-hover overflow-hidden flex flex-col h-full">

    {{-- Media --}}
    <a href="{{ $href }}" class="block relative ratio-16x9 group" tabindex="-1" aria-hidden="true">
        @if($event->flyerUrl())
            <img src="{{ $event->flyerUrl() }}" alt="" loading="lazy" decoding="async"
                 class="transition-transform duration-500 group-hover:scale-[1.04]">
        @else
            <span class="w-full h-full flex items-center justify-center text-brand-300"
                  style="background:linear-gradient(135deg,#ffe4ef,#efeaf6)">
                <x-ui.icon name="trophy" :size="44" :stroke="1.4" />
            </span>
        @endif

        <span class="absolute top-3 left-3">
            @if($isLive)
                <x-ui.badge tone="live" dot>Voting open</x-ui.badge>
            @else
                <x-ui.badge tone="closed">Voting closed</x-ui.badge>
            @endif
        </span>

        @if($event->isPayPerVote())
            <span class="absolute top-3 right-3 badge"
                  style="background:rgba(255,255,255,.94);color:#241038;backdrop-filter:blur(6px)">
                GH&#8373;{{ $event->priceInGhs() }} / vote
            </span>
        @else
            <span class="absolute top-3 right-3 badge" style="background:rgba(255,255,255,.94);color:#0c7f47">
                Free to vote
            </span>
        @endif
    </a>

    {{-- Body --}}
    <div class="p-5 flex flex-col gap-3 flex-1">
        <p class="eyebrow">{{ $event->typeLabel() }}</p>

        <h3 class="text-[17px] font-extrabold text-ink-900 leading-snug clamp-2">
            <a href="{{ $href }}" class="hover:text-brand-700 transition">{{ $event->name }}</a>
        </h3>

        @if($event->description)
            <p class="text-[13.5px] text-ink-500 leading-relaxed clamp-2">{{ $event->description }}</p>
        @endif

        <dl class="flex flex-col gap-2 text-[13px] text-ink-500 mt-auto pt-1">
            @if($organiser)
                <div class="flex items-center gap-2">
                    <dt class="sr-only">Organiser</dt>
                    <x-ui.icon name="building" :size="14" class="text-ink-300" />
                    <dd class="truncate">{{ $organiser }}</dd>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <dt class="sr-only">Categories</dt>
                <x-ui.icon name="grid" :size="14" class="text-ink-300" />
                <dd>{{ $cats }} {{ Str::plural('category', $cats) }}</dd>
            </div>
            @if($event->ends_at)
                <div class="flex items-center gap-2">
                    <dt class="sr-only">{{ $isLive ? 'Voting closes' : 'Voting closed' }}</dt>
                    <x-ui.icon name="calendar" :size="14" class="text-ink-300" />
                    <dd>{{ $isLive ? 'Closes' : 'Closed' }} {{ $event->ends_at->format('d M Y') }}</dd>
                </div>
            @endif
        </dl>

        {{-- Actions --}}
        <div class="flex items-center gap-2 pt-3 mt-1 border-t border-ink-100/70">
            <x-ui.btn :href="$href" variant="outline" size="sm" class="flex-1">View Award</x-ui.btn>
            @if($isLive)
                <x-ui.btn :href="route('vote.event', $event->slug)" variant="primary" size="sm" class="flex-1">
                    Vote Now
                </x-ui.btn>
            @elseif($event->resultsArePublic())
                <x-ui.btn :href="route('results.show', $event->slug)" variant="secondary" size="sm" class="flex-1">
                    Results
                </x-ui.btn>
            @endif
        </div>
    </div>
</article>
