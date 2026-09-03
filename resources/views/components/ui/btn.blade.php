@props([
    'variant' => 'primary',   // primary | secondary | outline | ghost | success | danger | onDark
    'size'    => null,        // sm | lg
    'href'    => null,
    'type'    => 'button',
    'block'   => false,
    'icon'    => null,        // leading icon name
    'iconEnd' => null,        // trailing icon name
    'loading' => null,        // wire:target value — shows a spinner while that action runs
    'disabled'=> false,
])

@php
    $classes = trim(implode(' ', array_filter([
        'btn',
        'btn-' . $variant,
        $size ? 'btn-' . $size : null,
        $block ? 'btn-block' : null,
    ])));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}
       @if($disabled) aria-disabled="true" tabindex="-1" @endif>
        @if($icon)<x-ui.icon :name="$icon" :size="16" />@endif
        {{ $slot }}
        @if($iconEnd)<x-ui.icon :name="$iconEnd" :size="16" />@endif
    </a>
@else
    <button type="{{ $type }}"
            @if($loading !== null) wire:loading.attr="disabled" wire:target="{{ $loading }}" @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading !== null)
            <span class="btn-spin" wire:loading wire:target="{{ $loading }}"></span>
            <span class="inline-flex items-center gap-2" wire:loading.remove wire:target="{{ $loading }}">
                @if($icon)<x-ui.icon :name="$icon" :size="16" />@endif
                {{ $slot }}
                @if($iconEnd)<x-ui.icon :name="$iconEnd" :size="16" />@endif
            </span>
        @else
            @if($icon)<x-ui.icon :name="$icon" :size="16" />@endif
            {{ $slot }}
            @if($iconEnd)<x-ui.icon :name="$iconEnd" :size="16" />@endif
        @endif
    </button>
@endif
