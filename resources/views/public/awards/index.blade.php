<x-layouts.public>
<x-slot name="title">{{ $heading }}</x-slot>
<x-slot name="description">{{ $intro }}</x-slot>

<x-site.page-header :eyebrow="$locked ? 'Award campaigns' : 'Voting campaigns'"
                    :title="$heading" :intro="$intro" />

<div class="site py-8 sm:py-12">

    {{-- ── Filter bar ─────────────────────────────────────────────
         A plain GET form so filters work with JavaScript disabled;
         the small script below just removes the need to press Apply. --}}
    <form method="GET" action="{{ url()->current() }}" id="award-filters"
          class="card p-4 sm:p-5 mb-8" role="search" aria-label="Filter {{ Str::lower($heading) }}">
        <div class="grid gap-3 lg:grid-cols-[minmax(0,2fr)_repeat(3,minmax(0,1fr))_auto]">

            <div class="relative">
                <label for="f-q" class="sr-only">Search {{ Str::lower($heading) }}</label>
                <span aria-hidden="true" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400">
                    <x-ui.icon name="search" :size="17" />
                </span>
                <input id="f-q" type="search" name="q" value="{{ $search }}" class="input pl-11"
                       placeholder="Search by name or organiser…">
            </div>

            @unless($locked)
                <div>
                    <label for="f-type" class="sr-only">Campaign type</label>
                    <select id="f-type" name="type" class="input">
                        <option value="all">All types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected($type === $t)>
                                {{ ['award' => 'Awards', 'election' => 'Elections', 'agm' => 'AGMs'][$t] ?? Str::headline($t) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endunless

            <div>
                <label for="f-status" class="sr-only">Voting status</label>
                <select id="f-status" name="status" class="input">
                    <option value="all"    @selected($status === 'all')>Any status</option>
                    <option value="live"   @selected($status === 'live')>Voting open</option>
                    <option value="closed" @selected($status === 'closed')>Voting closed</option>
                </select>
            </div>

            <div>
                <label for="f-sort" class="sr-only">Sort</label>
                <select id="f-sort" name="sort" class="input">
                    <option value="closing" @selected($sort === 'closing')>Closing soonest</option>
                    <option value="newest"  @selected($sort === 'newest')>Newest first</option>
                    <option value="name"    @selected($sort === 'name')>Name A–Z</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary flex-1 lg:flex-none">Apply</button>
                @if($search || $type !== ($locked ? 'award' : 'all') || $status !== 'all' || $sort !== 'closing')
                    <a href="{{ url()->current() }}" class="btn btn-ghost">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- ── Results ── --}}
    <div class="flex items-baseline justify-between gap-4 mb-5">
        <p class="text-[14px] text-ink-500">
            <strong class="text-ink-900 font-bold">{{ $events->total() }}</strong>
            {{ Str::plural(Str::lower(Str::singular($heading)), $events->total()) }} found
        </p>
    </div>

    @if($events->isEmpty())
        <div class="card">
            <x-ui.empty icon="trophy"
                        :title="$search ? 'No matches for “' . $search . '”' : 'No active ' . Str::lower($heading) . ' at the moment'"
                        :message="$search
                            ? 'Try a different name, or clear the filters to see everything.'
                            : 'New campaigns open regularly — check back soon.'">
                @if($search || $status !== 'all')
                    <x-ui.btn :href="url()->current()" variant="primary">Clear filters</x-ui.btn>
                @endif
                <x-ui.btn :href="route('nominees.index')" variant="outline">Search nominees</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $event)
                <x-cards.award :event="$event" />
            @endforeach
        </div>

        @if($events->hasPages())
            <div class="mt-10">{{ $events->onEachSide(1)->links() }}</div>
        @endif
    @endif
</div>

<x-slot name="scripts">
<script>
    // Auto-apply when a dropdown changes; the Apply button still works without JS.
    document.querySelectorAll('#award-filters select').forEach(function (el) {
        el.addEventListener('change', function () { el.form.requestSubmit(); });
    });
</script>
</x-slot>

</x-layouts.public>
