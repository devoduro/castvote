<x-layouts.public>
<x-slot name="title">Live Award Results</x-slot>
<x-slot name="description">Browse every CastVote award with published standings and see how nominees are ranking, category by category.</x-slot>

{{-- ═══ SEARCH HERO ═══ --}}
<section class="relative overflow-hidden" style="background:linear-gradient(150deg,#241038,#14031f)"
         aria-labelledby="results-heading">
    <div aria-hidden="true" class="absolute inset-0 opacity-25"
         style="background-image:radial-gradient(circle,rgba(225,29,116,.55) 1px,transparent 1px);background-size:26px 26px"></div>

    <div class="site relative py-12 sm:py-16">
        <div class="max-w-2xl mx-auto text-center">
            <p class="eyebrow text-brand-400">Standings</p>
            <h1 id="results-heading" class="text-white font-extrabold mt-3 leading-tight"
                style="font-size:clamp(26px,4.2vw,40px)">
                Live Award Results
            </h1>
            <p class="text-white/65 text-[15px] leading-relaxed mt-3">
                Follow the latest standings and see how nominees are performing.
            </p>

            <form method="GET" action="{{ route('results.index') }}" role="search"
                  class="mt-7 flex flex-col sm:flex-row gap-2.5" aria-label="Search award results">
                <div class="relative flex-1">
                    <label for="rs-q" class="sr-only">Search results by award name</label>
                    <span aria-hidden="true" class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400">
                        <x-ui.icon name="search" :size="18" />
                    </span>
                    <input id="rs-q" type="search" name="q" value="{{ $search }}"
                           class="input pl-12" style="height:52px" placeholder="Search results by award name…">
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Search</button>
                @if($search)
                    <a href="{{ route('results.index') }}" class="btn btn-onDark btn-lg">Clear</a>
                @endif
            </form>
        </div>
    </div>
</section>

<div class="site py-10 sm:py-14">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
        <div>
            <h2 class="text-ink-900 font-extrabold" style="font-size:clamp(22px,3vw,30px)">
                {{ $search ? 'Search results' : 'Award results' }}
            </h2>
            <p class="text-[14px] text-ink-500 mt-1.5">
                <strong class="text-ink-900 font-bold">{{ $events->count() }}</strong>
                {{ Str::plural('award', $events->count()) }} with published standings
            </p>
        </div>
        <x-ui.btn :href="route('voting.index')" variant="outline" icon-end="arrow-right">Cast a vote</x-ui.btn>
    </div>

    @if($events->isEmpty())
        <div class="card">
            <x-ui.empty icon="eye-off"
                        :title="$search ? 'No published results match “' . $search . '”' : 'No results have been published yet'"
                        message="Standings appear here once an organiser releases them. Vote counts stay private until then, and campaigns run as an anonymous tally never publish per-nominee figures.">
                @if($search)
                    <x-ui.btn :href="route('results.index')" variant="primary">Clear search</x-ui.btn>
                @endif
                <x-ui.btn :href="route('voting.index')" variant="outline">Awards open for voting</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        <div class="grid gap-4 sm:gap-5 grid-cols-2 lg:grid-cols-4">
            @foreach($events as $event)
                <x-cards.award-tile
                    :event="$event"
                    :href="route('results.show', $event->slug)"
                    cta="View results"
                    :badge="$event->isLive() ? 'Voting open' : null"
                    meta-label="Total categories"
                    :meta-value="$event->categories_count" />
            @endforeach
        </div>
    @endif
</div>

</x-layouts.public>
