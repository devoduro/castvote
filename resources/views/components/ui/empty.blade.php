@props([
    'icon'    => 'search',
    'title'   => 'Nothing here yet',
    'message' => null,
])

<div {{ $attributes->merge(['class' => 'text-center px-6 py-16 sm:py-20']) }}>
    <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-5">
        <x-ui.icon :name="$icon" :size="30" :stroke="1.6" />
    </div>
    <h3 class="text-lg sm:text-xl font-extrabold text-ink-900">{{ $title }}</h3>
    @if($message)
        <p class="text-[14.5px] text-ink-500 mt-2 max-w-sm mx-auto leading-relaxed">{{ $message }}</p>
    @endif
    @if(trim($slot) !== '')
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
