@props([
    'model',              // Livewire property name
    'label',
    'hint'   => null,
    'live'   => false,
    'checked'=> false,
])

@php $id = 'tg-' . Str::slug($model); @endphp

<div class="flex items-start gap-3.5">
    <label for="{{ $id }}" class="relative shrink-0 mt-0.5 cursor-pointer">
        <input id="{{ $id }}" type="checkbox"
               wire:model{{ $live ? '.live' : '' }}="{{ $model }}"
               class="peer sr-only">
        <span aria-hidden="true"
              class="block w-[44px] h-[25px] rounded-full transition-colors
                     {{ $checked ? 'bg-brand-600' : 'bg-ink-200' }}
                     peer-focus-visible:ring-4 peer-focus-visible:ring-brand-100"></span>
        <span aria-hidden="true"
              class="absolute top-[3px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all
                     {{ $checked ? 'left-[22px]' : 'left-[3px]' }}"></span>
    </label>
    <label for="{{ $id }}" class="cursor-pointer">
        <span class="block text-[13.5px] font-bold text-ink-900">{{ $label }}</span>
        @if($hint)<span class="block text-[12.5px] text-ink-400 mt-0.5 leading-relaxed">{{ $hint }}</span>@endif
    </label>
</div>
