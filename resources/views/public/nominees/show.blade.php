<x-layouts.public>
<x-slot name="title">{{ $nominee->name }}</x-slot>
<x-slot name="description">{{ Str::limit($nominee->bio ?: ($nominee->name . ' — nominee for ' . $category->name . ' at ' . $event->name . '. Cast your vote on CastVote.'), 155) }}</x-slot>

@php
    $isLive     = $event->status === 'live' && $event->isLive();
    $needsGate  = $event->requiresEligibilityList();
    $shortcode  = $event->ussd_shortcode ?: config('castvote.ussd_shortcode');
    $shareText  = rawurlencode('Vote for ' . $nominee->name . ' (' . $nominee->code . ') in ' . $event->name);
    $shareUrl   = rawurlencode(route('nominees.show', $nominee));
@endphp

{{-- Breadcrumb --}}
<div class="border-b border-ink-100 bg-white">
    <nav aria-label="Breadcrumb" class="site py-3.5">
        <ol class="flex items-center gap-1.5 text-[13px] text-ink-400 flex-wrap">
            <li><a href="{{ route('home') }}" class="hover:text-brand-700 transition">Home</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('nominees.index') }}" class="hover:text-brand-700 transition">Nominees</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ route('awards.category', [$event->slug, $category]) }}" class="hover:text-brand-700 transition">{{ $category->name }}</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink-700 font-medium" aria-current="page">{{ $nominee->name }}</li>
        </ol>
    </nav>
</div>

<div class="site py-8 sm:py-12">
    <div class="grid lg:grid-cols-[minmax(0,1fr)_400px] gap-8 lg:gap-10 items-start">

        {{-- ═══ PROFILE ═══ --}}
        <div>
            <div class="card overflow-hidden">
                <div class="ratio-4x3 sm:aspect-[3/2]">
                    @if($nominee->photo_path)
                        <img src="{{ asset('storage/' . $nominee->photo_path) }}"
                             alt="Portrait of {{ $nominee->name }}" fetchpriority="high" decoding="async">
                    @else
                        <span class="w-full h-full flex items-center justify-center font-display font-extrabold text-brand-400"
                              style="background:linear-gradient(135deg,#ffe4ef,#efeaf6);font-size:88px" aria-hidden="true">
                            {{ Str::upper(Str::substr($nominee->name, 0, 1)) }}
                        </span>
                    @endif
                </div>

                <div class="p-5 sm:p-7">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($isLive)
                            <x-ui.badge tone="live" dot>Voting open</x-ui.badge>
                        @else
                            <x-ui.badge tone="closed">Voting closed</x-ui.badge>
                        @endif
                        <x-ui.badge tone="brand">{{ $category->name }}</x-ui.badge>
                    </div>

                    <h1 class="text-ink-900 font-extrabold leading-tight" style="font-size:clamp(26px,4vw,38px)">
                        {{ $nominee->name }}
                    </h1>

                    <p class="text-[14px] text-ink-500 mt-2">
                        Nominee code
                        <span class="font-mono font-bold text-brand-700 bg-brand-50 px-2 py-0.5 rounded-md ml-1">{{ $nominee->code }}</span>
                    </p>

                    @if($showVotes)
                        <p class="flex items-center gap-2 text-[14.5px] font-bold text-ink-800 mt-4">
                            <x-ui.icon name="chart" :size="17" class="text-brand-600" />
                            {{ number_format($nominee->totalVotes()) }} votes so far
                        </p>
                    @endif

                    {{-- Award & category facts --}}
                    <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-4 mt-6 pt-6 border-t border-ink-100">
                        <div>
                            <dt class="text-[11.5px] font-bold uppercase tracking-wider text-ink-400">Award</dt>
                            <dd class="text-[14.5px] font-semibold text-ink-800 mt-1">
                                <a href="{{ route('awards.show', $event->slug) }}" class="hover:text-brand-700 transition">{{ $event->name }}</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11.5px] font-bold uppercase tracking-wider text-ink-400">Category</dt>
                            <dd class="text-[14.5px] font-semibold text-ink-800 mt-1">
                                <a href="{{ route('awards.category', [$event->slug, $category]) }}" class="hover:text-brand-700 transition">{{ $category->name }}</a>
                            </dd>
                        </div>
                        @if($event->organization?->name)
                            <div>
                                <dt class="text-[11.5px] font-bold uppercase tracking-wider text-ink-400">Organiser</dt>
                                <dd class="text-[14.5px] font-semibold text-ink-800 mt-1">{{ $event->organization->name }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-[11.5px] font-bold uppercase tracking-wider text-ink-400">Cost per vote</dt>
                            <dd class="text-[14.5px] font-semibold text-ink-800 mt-1">
                                {{ $event->isPayPerVote() ? 'GH₵' . $event->priceInGhs() : 'Free' }}
                            </dd>
                        </div>
                        @if($event->ends_at)
                            <div>
                                <dt class="text-[11.5px] font-bold uppercase tracking-wider text-ink-400">Voting {{ $isLive ? 'closes' : 'closed' }}</dt>
                                <dd class="text-[14.5px] font-semibold text-ink-800 mt-1">{{ $event->ends_at->format('d M Y, g:ia') }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if($nominee->bio)
                        <div class="mt-6 pt-6 border-t border-ink-100">
                            <h2 class="text-[16px] font-extrabold text-ink-900 mb-2.5">About {{ Str::before($nominee->name, ' ') ?: $nominee->name }}</h2>
                            <p class="text-[14.5px] text-ink-600 leading-relaxed whitespace-pre-line">{{ $nominee->bio }}</p>
                        </div>
                    @endif

                    {{-- Share --}}
                    <div class="mt-6 pt-6 border-t border-ink-100">
                        <h2 class="text-[13px] font-bold uppercase tracking-wider text-ink-400 mb-3">Share this nominee</h2>
                        <div class="flex flex-wrap gap-2">
                            <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                               class="btn btn-outline btn-sm">WhatsApp</a>
                            <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                               class="btn btn-outline btn-sm">X</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                               class="btn btn-outline btn-sm">Facebook</a>
                            <button type="button" class="btn btn-outline btn-sm" data-copy="{{ route('nominees.show', $nominee) }}">
                                <x-ui.icon name="share" :size="15" /> Copy link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Other nominees in the category --}}
            @if($peers->isNotEmpty())
                <section class="mt-10" aria-labelledby="peers">
                    <div class="flex items-end justify-between gap-4 mb-5">
                        <h2 id="peers" class="text-[20px] font-extrabold text-ink-900">Also nominated in {{ $category->name }}</h2>
                        <a href="{{ route('awards.category', [$event->slug, $category]) }}"
                           class="text-[13.5px] font-bold text-brand-700 hover:text-brand-800 transition inline-flex items-center gap-1">
                            See all <x-ui.icon name="arrow-right" :size="14" />
                        </a>
                    </div>
                    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
                        @foreach($peers as $peer)
                            <x-cards.nominee :nominee="$peer" :event="$event" :show-votes="$showVotes" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- ═══ VOTING PANEL ═══ --}}
        <aside class="lg:sticky lg:top-20 flex flex-col gap-5">
            @if(! $isLive)
                <div class="card p-6 text-center">
                    <span class="w-12 h-12 rounded-2xl bg-ink-50 text-ink-400 flex items-center justify-center mx-auto mb-4">
                        <x-ui.icon name="clock" :size="22" />
                    </span>
                    <h2 class="text-[17px] font-extrabold text-ink-900">Voting is closed</h2>
                    <p class="text-[13.5px] text-ink-500 mt-2 leading-relaxed">
                        {{ $event->ends_at ? 'Voting for this award closed on ' . $event->ends_at->format('d M Y') . '.' : 'Voting for this award is not open.' }}
                    </p>
                    <div class="flex flex-col gap-2 mt-5">
                        @if($event->resultsArePublic() && ! $event->isAnonymousTally())
                            <x-ui.btn :href="route('results.show', $event->slug)" variant="primary" block icon="chart">View results</x-ui.btn>
                        @endif
                        <x-ui.btn :href="route('awards.index')" variant="outline" block>Browse open awards</x-ui.btn>
                    </div>
                </div>
            @elseif($needsGate)
                <div class="card p-6 text-center">
                    <span class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4">
                        <x-ui.icon name="shield" :size="22" />
                    </span>
                    <h2 class="text-[17px] font-extrabold text-ink-900">Verified voters only</h2>
                    <p class="text-[13.5px] text-ink-500 mt-2 leading-relaxed">
                        This campaign checks voters against the organiser's eligibility list before a ballot is issued.
                    </p>
                    <x-ui.btn :href="route('vote.event', $event->slug)" variant="primary" block class="mt-5" icon-end="arrow-right">
                        Verify and vote
                    </x-ui.btn>
                </div>
            @else
                <livewire:vote.nominee-vote :nominee="$nominee" />
            @endif

            {{-- USSD --}}
            @if($isLive)
                <div class="rounded-2xl p-5 text-white" style="background:linear-gradient(140deg,#3c1f56,#14031f)">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="w-9 h-9 rounded-xl bg-brand-600/25 text-brand-400 flex items-center justify-center">
                            <x-ui.icon name="mobile" :size="17" />
                        </span>
                        <div>
                            <p class="text-[13.5px] font-extrabold">Vote by USSD</p>
                            <p class="text-[11.5px] text-white/45">No internet needed</p>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white/[.07] p-4 text-center mb-4">
                        <p class="text-[11px] uppercase tracking-wider text-white/45 font-bold mb-1">Dial</p>
                        <p class="font-mono text-2xl font-extrabold text-brand-400 tracking-wide">{{ $shortcode }}</p>
                    </div>
                    <ol class="flex flex-col gap-2.5 text-[13px] text-white/65">
                        <li class="flex gap-2.5"><span class="text-brand-400 font-bold">1.</span> Dial the shortcode from any Ghana network.</li>
                        <li class="flex gap-2.5"><span class="text-brand-400 font-bold">2.</span> Choose the <span class="text-white">{{ $category->name }}</span> category.</li>
                        <li class="flex gap-2.5"><span class="text-brand-400 font-bold">3.</span> Enter code <span class="font-mono font-bold text-white">{{ $nominee->code }}</span>.</li>
                        @if($event->isPayPerVote())
                            <li class="flex gap-2.5"><span class="text-brand-400 font-bold">4.</span> Approve the GH&#8373;{{ $event->priceInGhs() }} MoMo prompt.</li>
                        @endif
                    </ol>
                </div>
            @endif
        </aside>
    </div>
</div>

<x-slot name="scripts">
<script>
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(btn.dataset.copy);
                window.dispatchEvent(new CustomEvent('cv-toast', {
                    detail: { type: 'success', message: 'Link copied to your clipboard.' },
                }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('cv-toast', {
                    detail: { type: 'error', message: 'Could not copy the link — please copy it from the address bar.' },
                }));
            }
        });
    });
</script>
</x-slot>

</x-layouts.public>
