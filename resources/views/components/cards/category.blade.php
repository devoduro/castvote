@props(['category', 'event'])

@php
    $count = $category->nominees_count ?? $category->nominees()->count();
    $href  = route('awards.category', [$event->slug, $category]);
@endphp

<a href="{{ $href }}"
   class="card card-hover p-5 flex items-center gap-4 group focus-visible:shadow-none">
    <span class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0
                 group-hover:bg-brand-600 group-hover:text-white transition">
        <x-ui.icon name="trophy" :size="20" />
    </span>
    <span class="min-w-0 flex-1">
        <span class="block text-[15px] font-extrabold text-ink-900 leading-snug clamp-2 group-hover:text-brand-700 transition">
            {{ $category->name }}
        </span>
        <span class="block text-[12.5px] text-ink-500 mt-0.5">
            {{ $count }} {{ Str::plural('nominee', $count) }}
            @if($category->code)
                <span class="text-ink-300">·</span>
                <span class="font-mono text-ink-400">{{ $category->code }}</span>
            @endif
        </span>
    </span>
    <x-ui.icon name="chevron-right" :size="17" class="text-ink-300 group-hover:text-brand-600 transition" />
</a>
