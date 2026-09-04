<x-layouts.public>
<x-slot name="title">Vote — {{ $event->name }}</x-slot>
<x-slot name="description">Cast your vote in {{ $event->name }} on ClickVote.</x-slot>

@php $shortcode = $event->ussd_shortcode ?: config('clickvote.ussd_shortcode'); @endphp

{{-- ═══ BANNER ═══ --}}
<section class="relative overflow-hidden" style="background:#14031f">
    @if($event->flyerUrl())
        <img src="{{ $event->flyerUrl() }}" alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover opacity-40">
    @else
        <div aria-hidden="true" class="absolute inset-0 opacity-30"
             style="background-image:radial-gradient(circle,rgba(225,29,116,.6) 1px,transparent 1px);background-size:24px 24px"></div>
    @endif
    <div aria-hidden="true" class="absolute inset-0"
         style="background:linear-gradient(to top,rgba(20,3,31,.96) 15%,rgba(20,3,31,.7) 60%,rgba(20,3,31,.45) 100%)"></div>

    <div class="site relative py-7 sm:py-10">
        <a href="{{ route('awards.show', $event->slug) }}"
           class="inline-flex items-center gap-1.5 text-white/55 hover:text-white text-[13px] mb-4 transition">
            <x-ui.icon name="arrow-left" :size="15" /> Back to award
        </a>

        <div class="flex flex-wrap items-center gap-2 mb-3">
            <x-ui.badge tone="onDark">{{ $event->typeLabel() }}</x-ui.badge>
            <x-ui.badge tone="live" dot>Voting open</x-ui.badge>
        </div>

        <h1 class="text-white font-extrabold leading-tight" style="font-size:clamp(26px,4.2vw,38px)">
            {{ $event->name }}
        </h1>

        <dl class="flex flex-wrap gap-x-6 gap-y-2 mt-4 text-[13.5px] text-white/65">
            @if($event->ends_at)
                <div class="flex items-center gap-2">
                    <x-ui.icon name="clock" :size="15" class="text-brand-400" />
                    <dt class="sr-only">Voting closes</dt>
                    <dd>Closes {{ $event->ends_at->format('d M Y, g:ia') }}</dd>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <x-ui.icon name="cash" :size="15" class="text-brand-400" />
                <dt class="sr-only">Cost per vote</dt>
                <dd>{{ $event->isPayPerVote() ? 'GH₵' . $event->priceInGhs() . ' per vote' : 'Free to vote' }}</dd>
            </div>
            @if($event->organization?->name)
                <div class="flex items-center gap-2">
                    <x-ui.icon name="building" :size="15" class="text-brand-400" />
                    <dt class="sr-only">Organiser</dt>
                    <dd>{{ $event->organization->name }}</dd>
                </div>
            @endif
        </dl>
    </div>
</section>

{{-- ═══ USSD STRIP ═══ --}}
<div style="background:#3c1f56">
    <div class="site py-3.5 flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
        <p class="flex items-center gap-2.5 text-white/70 text-[13.5px]">
            <x-ui.icon name="mobile" :size="16" class="text-brand-400" />
            No internet? Dial
            <span class="font-mono font-extrabold text-white text-[16px] tracking-wide">{{ $shortcode }}</span>
        </p>
        <p class="flex items-center gap-2 text-white/45 text-[12.5px]">
            <x-ui.icon name="shield" :size="14" class="text-green-400" />
            Secured by Paystack · MTN · Telecel · AirtelTigo
        </p>
    </div>
</div>

{{-- ═══ BALLOT ═══ --}}
<div class="site py-8 sm:py-12">
    <div class="grid lg:grid-cols-[minmax(0,1fr)_320px] gap-8 lg:gap-10 items-start">

        <div>
            @if($event->requiresEligibilityList())
                <livewire:vote.eligibility-gate :event="$event" />
            @else
                <livewire:vote.ballot :event="$event" />
            @endif
        </div>

        {{-- USSD guide --}}
        <aside class="rounded-2xl p-5 text-white lg:sticky lg:top-20"
               style="background:linear-gradient(140deg,#3c1f56,#14031f)" aria-labelledby="ussd-guide">
            <div class="flex items-center gap-2.5 mb-4">
                <span class="w-9 h-9 rounded-xl bg-brand-600/25 text-brand-400 flex items-center justify-center">
                    <x-ui.icon name="mobile" :size="17" />
                </span>
                <div>
                    <h2 id="ussd-guide" class="text-[13.5px] font-extrabold">Vote by USSD</h2>
                    <p class="text-[11.5px] text-white/45">No internet needed</p>
                </div>
            </div>

            <div class="rounded-xl bg-white/[.07] p-4 text-center mb-4">
                <p class="text-[11px] uppercase tracking-wider text-white/45 font-bold mb-1">Dial this code</p>
                <p class="font-mono text-2xl font-extrabold text-brand-400 tracking-wide">{{ $shortcode }}</p>
                <p class="text-[11.5px] text-white/40 mt-1">Works on any Ghana network</p>
            </div>

            <ol class="flex flex-col gap-3 mb-4">
                @php
                    $steps = [
                        'Dial the shortcode above from your phone.',
                        'Select the category you want to vote in.',
                        'Enter the nominee code to cast your vote.',
                    ];
                    if ($event->isPayPerVote()) {
                        $steps[] = 'Approve the GH₵' . $event->priceInGhs() . ' Mobile Money prompt.';
                    }
                @endphp
                @foreach($steps as $i => $copy)
                    <li class="flex items-start gap-2.5">
                        <span class="w-[22px] h-[22px] rounded-full bg-brand-600/25 text-brand-400 text-[11px]
                                     font-extrabold flex items-center justify-center shrink-0 mt-px">{{ $i + 1 }}</span>
                        <span class="text-[13px] text-white/70 leading-relaxed">{{ $copy }}</span>
                    </li>
                @endforeach
            </ol>

            <p class="flex items-start gap-2 text-[12px] text-white/40 pt-4 border-t border-white/10">
                <x-ui.icon name="shield" :size="14" class="text-green-400 mt-px" />
                Secured by Paystack. Votes are final and cannot be reversed.
            </p>
        </aside>
    </div>
</div>

</x-layouts.public>
