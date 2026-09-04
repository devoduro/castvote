<x-layouts.public>
<x-slot name="title">{{ $event->name }} — Results</x-slot>
<x-slot name="description">Category-by-category standings for {{ $event->name }} on ClickVote.</x-slot>

{{-- Breadcrumb --}}
<div class="border-b border-ink-100 bg-white">
    <nav aria-label="Breadcrumb" class="site py-3.5">
        <ol class="flex items-center gap-1.5 text-[13px] text-ink-400 flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-brand-700 transition">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('results.index') }}" class="hover:text-brand-700 transition">Results</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink-700 font-medium truncate max-w-[50vw]" aria-current="page">{{ $event->name }}</li>
        </ol>
    </nav>
</div>

<div class="site py-8 sm:py-12">

    {{-- ── Award header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-7">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2.5">
                @if($event->isLive())
                    <x-ui.badge tone="live" dot>Voting open</x-ui.badge>
                @else
                    <x-ui.badge tone="closed">Final</x-ui.badge>
                @endif
                @if($event->organization?->name)
                    <x-ui.badge tone="neutral">{{ $event->organization->name }}</x-ui.badge>
                @endif
            </div>
            <h1 class="text-ink-900 font-extrabold leading-tight" style="font-size:clamp(24px,3.6vw,34px)">
                {{ $event->name }}
            </h1>
            <p class="text-[14px] text-ink-500 mt-2">
                {{ $categories->count() }} {{ Str::plural('category', $categories->count()) }} ·
                standings published by the organiser
            </p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <x-ui.btn :href="route('awards.show', $event->slug)" variant="outline">View award</x-ui.btn>
            @if($event->isLive())
                <x-ui.btn :href="route('vote.event', $event->slug)" variant="primary" icon-end="arrow-right">Vote now</x-ui.btn>
            @endif
        </div>
    </div>

    @if($categories->isEmpty())
        <div class="card">
            <x-ui.empty icon="grid" title="No categories published"
                        message="The organiser has not added categories to this campaign yet.">
                <x-ui.btn :href="route('results.index')" variant="outline">Back to results</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        {{-- ── Category selector ── --}}
        <div class="card p-4 sm:p-5 mb-8">
            <p class="text-[12px] font-bold uppercase tracking-wider text-ink-400 mb-3">Category</p>

            {{-- Pills on wide screens, a select on phones. --}}
            <ul class="hidden sm:flex flex-wrap gap-2" role="tablist">
                @foreach($categories as $cat)
                    @php $active = $category && $cat->id === $category->id; @endphp
                    <li>
                        <a href="{{ route('results.show', ['slug' => $event->slug, 'category' => $cat->id]) }}"
                           role="tab" aria-selected="{{ $active ? 'true' : 'false' }}"
                           class="btn btn-sm {{ $active ? 'btn-primary' : 'btn-outline' }}">
                            {{ $cat->name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <form method="GET" action="{{ route('results.show', $event->slug) }}" class="sm:hidden">
                <label for="cat-select" class="sr-only">Choose a category</label>
                <select id="cat-select" name="category" class="input"
                        onchange="this.form.requestSubmit()">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($category && $cat->id === $category->id)>
                            {{ $cat->name }} ({{ $cat->nominees_count }})
                        </option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="btn btn-primary btn-sm mt-2">Show</button></noscript>
            </form>
        </div>

        {{-- ── Standings ── --}}
        <div class="flex flex-wrap items-baseline justify-between gap-3 mb-5">
            <h2 class="text-[20px] sm:text-[24px] font-extrabold text-ink-900">{{ $category?->name }}</h2>
            <p class="text-[14px] text-ink-500">
                <strong class="text-ink-900">{{ number_format($totalVotes) }}</strong>
                {{ Str::plural('vote', $totalVotes) }} in this category
            </p>
        </div>

        @if($standings->isEmpty() || $totalVotes === 0)
            <div class="card">
                <x-ui.empty icon="chart" title="No votes counted yet"
                            message="As soon as votes come in for this category, the standings appear here.">
                    @if($event->isLive())
                        <x-ui.btn :href="route('vote.event', $event->slug)" variant="primary">Be the first to vote</x-ui.btn>
                    @endif
                </x-ui.empty>
            </div>
        @else
            {{-- Podium --}}
            @php $podium = $standings->take(3); @endphp
            @if($podium->count() >= 2)
                <ol class="grid gap-4 sm:grid-cols-3 mb-6" aria-label="Top three nominees">
                    @foreach($podium as $row)
                        @php
                            $medal = [
                                1 => ['#dc6803', '#fff5da', 'Leading'],
                                2 => ['#6b6480', '#f2f0f7', 'Second'],
                                3 => ['#a16207', '#fdf3e3', 'Third'],
                            ][$row->rank];
                        @endphp
                        <li class="card p-5 relative overflow-hidden {{ $row->rank === 1 ? 'sm:-mt-2 sm:pb-7' : '' }}"
                            @if($row->rank === 1) style="border-color:#fedf89;box-shadow:0 8px 28px rgba(220,104,3,.14)" @endif>
                            <span aria-hidden="true" class="absolute top-0 inset-x-0 h-1" style="background:{{ $medal[0] }}"></span>

                            <div class="flex items-center gap-3.5">
                                <span class="w-12 h-12 rounded-2xl overflow-hidden shrink-0 bg-ink-50">
                                    @if($row->nominee->photo_path)
                                        <img src="{{ asset('storage/' . $row->nominee->photo_path) }}" alt=""
                                             loading="lazy" class="w-full h-full object-cover">
                                    @else
                                        <span class="w-full h-full flex items-center justify-center font-display font-extrabold text-brand-400 text-lg">
                                            {{ Str::upper(Str::substr($row->nominee->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <span class="badge" style="background:{{ $medal[1] }};color:{{ $medal[0] }}">
                                        #{{ $row->rank }} {{ $medal[2] }}
                                    </span>
                                    <p class="text-[15.5px] font-extrabold text-ink-900 leading-snug mt-1.5 clamp-2">
                                        <a href="{{ route('nominees.show', $row->nominee) }}" class="hover:text-brand-700 transition">
                                            {{ $row->nominee->name }}
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <p class="font-display font-extrabold text-ink-900 mt-4 leading-none" style="font-size:28px">
                                {{ number_format($row->votes) }}
                                <span class="text-[13px] font-sans font-semibold text-ink-400 ml-1">votes</span>
                            </p>
                            <div class="h-2 rounded-full bg-ink-100 mt-3 overflow-hidden">
                                <div class="h-full rounded-full" style="width:{{ $row->share }}%;background:{{ $medal[0] }}"></div>
                            </div>
                            <p class="text-[12.5px] text-ink-400 mt-1.5">{{ $row->share }}% of category votes</p>
                        </li>
                    @endforeach
                </ol>
            @endif

            {{-- Full ranking --}}
            <div class="card overflow-hidden">
                <h3 class="px-5 sm:px-6 py-4 border-b border-ink-100 text-[15px] font-extrabold text-ink-900">
                    Full standings
                </h3>
                <ol class="divide-y divide-ink-100">
                    @foreach($standings as $row)
                        <li class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-4 {{ $row->rank <= 3 ? 'bg-brand-50/25' : '' }}">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-[13.5px] shrink-0
                                         {{ $row->rank <= 3 ? 'text-white' : 'bg-ink-50 text-ink-500' }}"
                                  @if($row->rank <= 3) style="background:{{ [1 => '#dc6803', 2 => '#6b6480', 3 => '#a16207'][$row->rank] }}" @endif>
                                {{ $row->rank }}
                            </span>

                            <span class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-ink-50 hidden sm:block">
                                @if($row->nominee->photo_path)
                                    <img src="{{ asset('storage/' . $row->nominee->photo_path) }}" alt=""
                                         loading="lazy" class="w-full h-full object-cover">
                                @else
                                    <span class="w-full h-full flex items-center justify-center font-display font-extrabold text-brand-400">
                                        {{ Str::upper(Str::substr($row->nominee->name, 0, 1)) }}
                                    </span>
                                @endif
                            </span>

                            <span class="flex-1 min-w-0">
                                <a href="{{ route('nominees.show', $row->nominee) }}"
                                   class="block text-[14.5px] font-bold text-ink-900 hover:text-brand-700 transition truncate">
                                    {{ $row->nominee->name }}
                                </a>
                                <span class="flex items-center gap-2 mt-1.5">
                                    <span class="h-1.5 rounded-full bg-ink-100 overflow-hidden flex-1 max-w-[220px]">
                                        <span class="block h-full rounded-full bg-brand-500" style="width:{{ $row->share }}%"></span>
                                    </span>
                                    <span class="text-[12px] text-ink-400 font-medium">{{ $row->share }}%</span>
                                </span>
                            </span>

                            <span class="text-right shrink-0">
                                <span class="block font-display text-[16px] font-extrabold text-ink-900 leading-none">
                                    {{ number_format($row->votes) }}
                                </span>
                                <span class="block text-[11.5px] text-ink-400 mt-1">votes</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            <p class="flex items-start gap-2 text-[12.5px] text-ink-400 mt-5 leading-relaxed">
                <x-ui.icon name="info" :size="15" class="mt-px" />
                <span>
                    Standings reflect votes confirmed by the payment provider and are published by
                    {{ $event->organization?->name ?? 'the organiser' }}.
                    @if($event->isLive()) Voting is still open, so positions can change. @endif
                </span>
            </p>
        @endif
    @endif
</div>

</x-layouts.public>
