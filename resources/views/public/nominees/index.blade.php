<x-layouts.public>
<x-slot name="title">Nominees</x-slot>
<x-slot name="description">Search every nominee on ClickVote by name or nominee code, then cast your vote.</x-slot>

<x-site.page-header eyebrow="Nominees" title="Find your nominee"
                    intro="Search by name or nominee code, then back the person you believe in." />

<div class="site py-8 sm:py-12">

    <form method="GET" action="{{ route('nominees.index') }}" id="nominee-filters"
          class="card p-4 sm:p-5 mb-8" role="search" aria-label="Search nominees">
        <div class="grid gap-3 lg:grid-cols-[minmax(0,2fr)_repeat(2,minmax(0,1fr))_auto]">

            <div class="relative">
                <label for="n-q" class="sr-only">Search by nominee name or code</label>
                <span aria-hidden="true" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-400">
                    <x-ui.icon name="search" :size="17" />
                </span>
                <input id="n-q" type="search" name="q" value="{{ $search }}" class="input pl-11"
                       placeholder="e.g. Kofi Kinaata or NK01">
            </div>

            <div>
                <label for="n-award" class="sr-only">Filter by award</label>
                <select id="n-award" name="award" class="input">
                    <option value="all">All awards</option>
                    @foreach($awards as $award)
                        <option value="{{ $award->slug }}" @selected($awardSlug === $award->slug)>{{ $award->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="n-category" class="sr-only">Filter by category</label>
                <select id="n-category" name="category" class="input">
                    <option value="all">All categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary flex-1 lg:flex-none">Search</button>
                @if($search || $awardSlug !== 'all' || $categoryId !== 'all')
                    <a href="{{ route('nominees.index') }}" class="btn btn-ghost">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <p class="text-[14px] text-ink-500 mb-5">
        <strong class="text-ink-900 font-bold">{{ number_format($nominees->total()) }}</strong>
        {{ Str::plural('nominee', $nominees->total()) }} found
    </p>

    @if($nominees->isEmpty())
        <div class="card">
            <x-ui.empty icon="users" title="No nominees found. Try another search."
                        message="Check the spelling, or clear the filters to browse every nominee.">
                <x-ui.btn :href="route('nominees.index')" variant="primary">Clear search</x-ui.btn>
                <x-ui.btn :href="route('awards.index')" variant="outline">Browse awards</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        <div class="grid gap-5 grid-cols-2 lg:grid-cols-4">
            @foreach($nominees as $nominee)
                <x-cards.nominee :nominee="$nominee" />
            @endforeach
        </div>

        @if($nominees->hasPages())
            <div class="mt-10">{{ $nominees->onEachSide(1)->links() }}</div>
        @endif
    @endif
</div>

<x-slot name="scripts">
<script>
    // Changing the award reloads with a category list scoped to it.
    document.querySelectorAll('#nominee-filters select').forEach(function (el) {
        el.addEventListener('change', function () {
            if (el.name === 'award') {
                el.form.querySelector('[name="category"]').value = 'all';
            }
            el.form.requestSubmit();
        });
    });
</script>
</x-slot>

</x-layouts.public>
