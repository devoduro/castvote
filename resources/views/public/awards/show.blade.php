<x-layouts.public>
<x-slot name="title">{{ $event->name }}</x-slot>
<x-slot name="description">{{ Str::limit($event->description ?? ($event->name . ' on ClickVote — view categories, nominees and cast your vote.'), 155) }}</x-slot>

@php
    $isLive    = $event->status === 'live' && $event->isLive();
    $shortcode = $event->ussd_shortcode ?: config('clickvote.ussd_shortcode');
@endphp

{{-- ═══ BANNER ═══ --}}
<section class="relative overflow-hidden" style="background:#14031f">
    @if($event->flyerUrl())
        <img src="{{ $event->flyerUrl() }}" alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover opacity-45">
    @else
        <div aria-hidden="true" class="absolute inset-0 opacity-30"
             style="background-image:radial-gradient(circle,rgba(225,29,116,.6) 1px,transparent 1px);background-size:24px 24px"></div>
    @endif
    <div aria-hidden="true" class="absolute inset-0"
         style="background:linear-gradient(to top,rgba(20,3,31,.96) 12%,rgba(20,3,31,.68) 55%,rgba(20,3,31,.4) 100%)"></div>

    <div class="site relative pt-6 pb-8 sm:pt-8 sm:pb-12" style="min-height:300px">
        <nav aria-label="Breadcrumb" class="mb-5">
            <ol class="flex items-center gap-1.5 text-[13px] text-white/55">
                <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('awards.index') }}" class="hover:text-white transition">Awards</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-white/85 truncate max-w-[45vw]" aria-current="page">{{ $event->name }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-end">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3.5">
                    <x-ui.badge tone="onDark">{{ $event->typeLabel() }}</x-ui.badge>
                    @if($isLive)
                        <x-ui.badge tone="live" dot>Voting open</x-ui.badge>
                    @else
                        <x-ui.badge tone="closed">Voting closed</x-ui.badge>
                    @endif
                </div>

                <h1 class="text-white font-extrabold leading-tight" style="font-size:clamp(28px,4.6vw,44px)">
                    {{ $event->name }}
                </h1>

                @if($event->organization?->name)
                    <p class="flex items-center gap-2 text-white/65 text-[14.5px] mt-3">
                        <x-ui.icon name="building" :size="16" class="text-brand-400" />
                        Organised by <span class="text-white font-semibold">{{ $event->organization->name }}</span>
                    </p>
                @endif

                @if($event->description)
                    <p class="text-white/70 text-[15px] leading-relaxed mt-4 max-w-2xl">{{ $event->description }}</p>
                @endif

                <dl class="flex flex-wrap gap-x-7 gap-y-3 mt-6 text-[14px]">
                    @if($event->ends_at)
                        <div class="flex items-center gap-2 text-white/65">
                            <x-ui.icon name="clock" :size="16" class="text-brand-400" />
                            <dt class="sr-only">Voting {{ $isLive ? 'closes' : 'closed' }}</dt>
                            <dd>{{ $isLive ? 'Closes' : 'Closed' }} {{ $event->ends_at->format('d M Y, g:ia') }}</dd>
                        </div>
                    @endif
                    <div class="flex items-center gap-2 text-white/65">
                        <x-ui.icon name="cash" :size="16" class="text-brand-400" />
                        <dt class="sr-only">Cost per vote</dt>
                        <dd>
                            @if($event->isPayPerVote())
                                <span class="text-white font-semibold">GH&#8373;{{ $event->priceInGhs() }}</span> per vote
                            @else
                                <span class="text-white font-semibold">Free to vote</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center gap-2 text-white/65">
                        <x-ui.icon name="grid" :size="16" class="text-brand-400" />
                        <dt class="sr-only">Categories</dt>
                        <dd>{{ $stats['categories'] }} {{ Str::plural('category', $stats['categories']) }} ·
                            {{ $stats['nominees'] }} {{ Str::plural('nominee', $stats['nominees']) }}</dd>
                    </div>
                    @if($stats['votes'] !== null)
                        <div class="flex items-center gap-2 text-white/65">
                            <x-ui.icon name="chart" :size="16" class="text-brand-400" />
                            <dt class="sr-only">Votes cast</dt>
                            <dd>{{ number_format($stats['votes']) }} votes cast</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 w-full lg:w-auto">
                @if($isLive)
                    <x-ui.btn :href="route('vote.event', $event->slug)" variant="primary" size="lg" icon-end="arrow-right">
                        Vote Now
                    </x-ui.btn>
                @endif
                @if($event->resultsArePublic() && ! $event->isAnonymousTally())
                    <x-ui.btn :href="route('results.show', $event->slug)" variant="onDark" size="lg" icon="chart">
                        View Results
                    </x-ui.btn>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══ USSD STRIP ═══ --}}
@if($isLive)
    <div style="background:#3c1f56">
        <div class="site py-3.5 flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
            <p class="flex items-center gap-2.5 text-white/70 text-[13.5px]">
                <x-ui.icon name="mobile" :size="16" class="text-brand-400" />
                No internet? Dial
                <span class="font-mono font-extrabold text-white text-[16px] tracking-wide">{{ $shortcode }}</span>
                on any Ghana network.
            </p>
            <p class="flex items-center gap-2 text-white/45 text-[12.5px]">
                <x-ui.icon name="shield" :size="14" class="text-green-400" />
                Payments secured by Paystack · MTN · Telecel · AirtelTigo
            </p>
        </div>
    </div>
@endif

{{-- ═══ CATEGORIES ═══ --}}
<div class="site py-10 sm:py-14">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
        <div>
            <p class="eyebrow">Categories</p>
            <h2 class="text-ink-900 font-extrabold mt-2" style="font-size:clamp(24px,3.2vw,32px)">
                Choose a category
            </h2>
            <p class="text-ink-500 text-[15px] mt-2 max-w-xl">
                Open a category to see its nominees, then back the one you believe in.
            </p>
        </div>
        @if($isLive)
            <x-ui.btn :href="route('vote.event', $event->slug)" variant="secondary" icon-end="arrow-right">
                Go to ballot
            </x-ui.btn>
        @endif
    </div>

    @if($categories->isEmpty())
        <div class="card">
            <x-ui.empty icon="grid" title="No categories published yet"
                        message="The organiser has not added categories to this campaign.">
                <x-ui.btn :href="route('awards.index')" variant="outline">Back to awards</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categories as $category)
                <x-cards.category :category="$category" :event="$event" />
            @endforeach
        </div>
    @endif
</div>

</x-layouts.public>
