<x-layouts.public>
<x-slot name="title">Cast Your Vote</x-slot>
<x-slot name="description">Pick an award open for voting on ClickVote, find your nominee and cast a secure vote by web or USSD.</x-slot>

{{-- ═══ SEARCH HERO ═══ --}}
<section class="relative overflow-hidden" style="background:linear-gradient(150deg,#241038,#14031f)"
         aria-labelledby="voting-heading">
    <div aria-hidden="true" class="absolute inset-0 opacity-25"
         style="background-image:radial-gradient(circle,rgba(225,29,116,.55) 1px,transparent 1px);background-size:26px 26px"></div>

    <div class="site relative py-12 sm:py-16">
        <div class="max-w-2xl mx-auto text-center">
            <p class="eyebrow text-brand-400">Voting is open</p>
            <h1 id="voting-heading" class="text-white font-extrabold mt-3 leading-tight"
                style="font-size:clamp(26px,4.2vw,40px)">
                Cast your vote in awards across Ghana
            </h1>

            <form method="GET" action="{{ route('voting.index') }}" role="search"
                  class="mt-7 flex flex-col sm:flex-row gap-2.5" aria-label="Search awards open for voting">
                <div class="relative flex-1">
                    <label for="v-q" class="sr-only">Search awards by name or organiser</label>
                    <span aria-hidden="true" class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400">
                        <x-ui.icon name="search" :size="18" />
                    </span>
                    <input id="v-q" type="search" name="q" value="{{ $search }}"
                           class="input pl-12" style="height:52px"
                           placeholder="Type an award name…">
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Search</button>
                @if($search)
                    <a href="{{ route('voting.index') }}" class="btn btn-onDark btn-lg">Clear</a>
                @endif
            </form>

            <p class="text-white/60 text-[14px] leading-relaxed mt-6 max-w-xl mx-auto">
                Every campaign here accepts votes on the web, and USSD where the organiser has enabled it —
                dial <span class="font-mono font-bold text-white">{{ $shortcode }}</span> on any Ghana network,
                so no voter is locked out. Pick an award below to see its nominees.
            </p>
        </div>
    </div>
</section>

{{-- ═══ AWARDS OPEN FOR VOTING ═══ --}}
<div class="site py-10 sm:py-14">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-7">
        <div>
            <h2 class="text-ink-900 font-extrabold" style="font-size:clamp(22px,3vw,30px)">
                {{ $search ? 'Search results' : 'Awards open for voting' }}
            </h2>
            <p class="text-[14px] text-ink-500 mt-1.5">
                <strong class="text-ink-900 font-bold">{{ $events->count() }}</strong>
                {{ Str::plural('campaign', $events->count()) }} accepting votes right now
            </p>
        </div>
        <x-ui.btn :href="route('awards.index')" variant="outline" icon-end="arrow-right">
            Browse all awards
        </x-ui.btn>
    </div>

    @if($events->isEmpty())
        <div class="card">
            <x-ui.empty icon="trophy"
                        :title="$search ? 'No open awards match “' . $search . '”' : 'No awards are open for voting right now'"
                        :message="$search
                            ? 'Try a different name, or browse everything that has run on ClickVote.'
                            : 'Voting campaigns open regularly. Browse past awards or check back soon.'">
                @if($search)
                    <x-ui.btn :href="route('voting.index')" variant="primary">Clear search</x-ui.btn>
                @endif
                <x-ui.btn :href="route('awards.index')" variant="outline">All awards</x-ui.btn>
                <x-ui.btn :href="route('results.index')" variant="outline">View results</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        <div class="grid gap-4 sm:gap-5 grid-cols-2 lg:grid-cols-4">
            @foreach($events as $event)
                <x-cards.award-tile
                    :event="$event"
                    :href="route('awards.show', $event->slug)"
                    cta="View award"
                    badge="Voting open"
                    meta-label="Cost per vote"
                    :meta-value="$event->isPayPerVote() ? 'GH₵' . $event->priceInGhs() : 'Free'" />
            @endforeach
        </div>
    @endif

    {{-- How to vote --}}
    <section class="mt-14 sm:mt-20" aria-labelledby="how-to-vote">
        <div class="text-center max-w-xl mx-auto mb-8">
            <p class="eyebrow">Two ways to vote</p>
            <h2 id="how-to-vote" class="text-ink-900 font-extrabold mt-2" style="font-size:clamp(22px,3vw,30px)">
                Smartphone or not, you can take part
            </h2>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="card p-6">
                <span class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                    <x-ui.icon name="grid" :size="20" />
                </span>
                <h3 class="text-[16.5px] font-extrabold text-ink-900">On the web</h3>
                <ol class="flex flex-col gap-2.5 mt-3">
                    @foreach([
                        'Open an award and pick a category.',
                        'Choose your nominee and how many votes to buy.',
                        'Enter your Mobile Money number and approve the prompt.',
                    ] as $i => $step)
                        <li class="flex items-start gap-2.5 text-[13.5px] text-ink-600 leading-relaxed">
                            <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-700 text-[11px] font-extrabold
                                         flex items-center justify-center shrink-0 mt-0.5">{{ $i + 1 }}</span>
                            {{ $step }}
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="card p-6">
                <span class="w-11 h-11 rounded-xl bg-ink-50 text-ink-700 flex items-center justify-center mb-4">
                    <x-ui.icon name="mobile" :size="20" />
                </span>
                <h3 class="text-[16.5px] font-extrabold text-ink-900">By USSD — no internet</h3>
                <p class="text-[13.5px] text-ink-500 mt-2 leading-relaxed">
                    Where the organiser has enabled it, dial the campaign shortcode from any phone on MTN,
                    Telecel or AirtelTigo and follow the prompts.
                </p>
                <p class="rounded-xl bg-ink-950 text-center py-4 mt-4">
                    <span class="block text-[11px] uppercase tracking-wider text-white/45 font-bold mb-1">Dial</span>
                    <span class="font-mono text-2xl font-extrabold text-brand-400 tracking-wide">{{ $shortcode }}</span>
                </p>
            </div>
        </div>
    </section>
</div>

</x-layouts.public>
