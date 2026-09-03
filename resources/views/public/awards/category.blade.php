<x-layouts.public>
<x-slot name="title">{{ $category->name }} — {{ $event->name }}</x-slot>
<x-slot name="description">Nominees in the {{ $category->name }} category of {{ $event->name }}.</x-slot>

@php $isLive = $event->status === 'live' && $event->isLive(); @endphp

<x-site.page-header eyebrow="{{ $event->name }}" :title="$category->name"
                    intro="{{ $nominees->count() }} {{ Str::plural('nominee', $nominees->count()) }} in this category.">
    <x-slot name="breadcrumb">
        <nav aria-label="Breadcrumb">
            <ol class="flex items-center gap-1.5 text-[13px] text-white/55 flex-wrap">
                <li><a href="{{ route('home') }}" class="hover:text-white transition">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('awards.index') }}" class="hover:text-white transition">Awards</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('awards.show', $event->slug) }}" class="hover:text-white transition truncate max-w-[38vw] inline-block align-bottom">{{ $event->name }}</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-white/85" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>
    </x-slot>

    <div class="flex flex-wrap items-center gap-3">
        @if($isLive)
            <x-ui.btn :href="route('vote.event', $event->slug)" variant="primary" icon-end="arrow-right">Vote in this award</x-ui.btn>
        @endif
        <x-ui.btn :href="route('awards.show', $event->slug)" variant="onDark" icon="arrow-left">All categories</x-ui.btn>
    </div>
</x-site.page-header>

<div class="site py-8 sm:py-12">
    @if($nominees->isEmpty())
        <div class="card">
            <x-ui.empty icon="users" title="No nominees found"
                        message="The organiser has not published nominees for this category yet.">
                <x-ui.btn :href="route('awards.show', $event->slug)" variant="outline">Back to categories</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        @unless($showVotes)
            <p class="flex items-start gap-2.5 text-[13.5px] text-ink-500 mb-6 card p-4">
                <x-ui.icon name="eye-off" :size="17" class="text-ink-400 mt-px" />
                <span>Vote counts for this campaign are not public. The organiser will publish standings when voting is released.</span>
            </p>
        @endunless

        <div class="grid gap-5 grid-cols-2 lg:grid-cols-4">
            @foreach($nominees as $nominee)
                <x-cards.nominee :nominee="$nominee" :event="$event"
                                 :show-votes="$showVotes" :votes="$tallies[$nominee->id] ?? 0" />
            @endforeach
        </div>
    @endif
</div>

</x-layouts.public>
