@props([
    'eyebrow' => null,
    'title',
    'intro'   => null,
    'compact' => false,
])

<section class="relative overflow-hidden" style="background:linear-gradient(150deg,#241038,#14031f)">
    <div aria-hidden="true" class="absolute inset-0 opacity-25"
         style="background-image:radial-gradient(circle,rgba(225,29,116,.55) 1px,transparent 1px);background-size:26px 26px"></div>
    <div class="site relative {{ $compact ? 'py-8 sm:py-10' : 'py-10 sm:py-14' }}">
        @if(isset($breadcrumb))
            <div class="mb-4">{{ $breadcrumb }}</div>
        @endif
        @if($eyebrow)<p class="eyebrow text-brand-400">{{ $eyebrow }}</p>@endif
        <h1 class="text-white font-extrabold {{ $eyebrow ? 'mt-2.5' : '' }}"
            style="font-size:clamp(28px,4.4vw,42px)">{{ $title }}</h1>
        @if($intro)
            <p class="text-white/65 text-[15px] sm:text-[16px] leading-relaxed mt-3 max-w-2xl">{{ $intro }}</p>
        @endif
        @if(trim($slot) !== '')
            <div class="mt-6">{{ $slot }}</div>
        @endif
    </div>
</section>
