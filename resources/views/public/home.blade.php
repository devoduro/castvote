<x-layouts.public>
<x-slot name="title">Awards Voting, Nominations &amp; Events</x-slot>
<x-slot name="description">Discover awards, support your favourite nominees, cast secure votes and participate in exciting events across Ghana.</x-slot>

{{-- ═══ HERO ═══ --}}
<section class="relative overflow-hidden" style="background:linear-gradient(160deg,#241038 0%,#14031f 55%,#3c1f56 100%)">
    <div aria-hidden="true" class="absolute inset-0 opacity-[.35]"
         style="background-image:radial-gradient(circle,rgba(225,29,116,.5) 1px,transparent 1px);background-size:26px 26px"></div>
    <div aria-hidden="true" class="absolute -top-24 -right-24 w-[420px] h-[420px] rounded-full blur-3xl opacity-30"
         style="background:radial-gradient(circle,#e11d74,transparent 70%)"></div>

    <div class="site relative pt-14 pb-16 sm:pt-20 sm:pb-24">
        <div class="grid lg:grid-cols-[1.05fr_.95fr] gap-12 lg:gap-14 items-center">

            <div class="cv-in">
                <p class="eyebrow text-brand-400">CastVote Ghana</p>
                <h1 class="text-white font-extrabold mt-3 leading-[1.08]"
                    style="font-size:clamp(32px,5.4vw,54px)">
                    Awards Voting,<br class="hidden sm:block"> Nominations &amp; Events
                    <span class="text-brand-500">Made Simple</span>
                </h1>
                <p class="text-white/70 mt-5 text-[15.5px] sm:text-[17px] leading-relaxed max-w-xl">
                    Discover awards, support your favourite nominees, cast secure votes and participate
                    in exciting events across Ghana.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mt-8">
                    <x-ui.btn :href="route('voting.index')" variant="primary" size="lg" icon-end="arrow-right">
                        Vote Now
                    </x-ui.btn>
                    <x-ui.btn :href="route('results.index')" variant="onDark" size="lg" icon="chart">
                        View Results
                    </x-ui.btn>
                </div>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-8 text-white/55 text-[13px]">
                    <span class="flex items-center gap-2"><x-ui.icon name="shield" :size="15" class="text-brand-400" /> Paystack-secured payments</span>
                    <span class="flex items-center gap-2"><x-ui.icon name="mobile" :size="15" class="text-brand-400" /> Vote by web or USSD</span>
                    <span class="flex items-center gap-2"><x-ui.icon name="cash" :size="15" class="text-brand-400" /> Mobile Money accepted</span>
                </div>
            </div>

            {{-- Live stats panel --}}
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach([
                    ['value' => number_format($stats['votes']),    'label' => 'Votes cast',      'icon' => 'check-circle', 'color' => '#f03d86'],
                    ['value' => number_format($stats['nominees']), 'label' => 'Nominees',        'icon' => 'users',        'color' => '#c3b1dd'],
                    ['value' => number_format($stats['events']),   'label' => 'Campaigns',       'icon' => 'trophy',       'color' => '#fdb022'],
                    ['value' => number_format($stats['live']),     'label' => 'Live right now',  'icon' => 'megaphone',    'color' => '#4ade80'],
                ] as $s)
                    <div class="rounded-2xl border border-white/10 bg-white/[.06] p-4 sm:p-5 backdrop-blur">
                        <x-ui.icon :name="$s['icon']" :size="20" style="color:{{ $s['color'] }}" />
                        <p class="font-display font-extrabold text-white mt-3 leading-none"
                           style="font-size:clamp(22px,3.4vw,30px)">{{ $s['value'] }}</p>
                        <p class="text-white/50 text-[12.5px] mt-1.5">{{ $s['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══ LIVE AWARDS ═══ --}}
<section class="site py-14 sm:py-20" aria-labelledby="live-awards">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <p class="eyebrow flex items-center gap-2">
                <span class="live-dot w-2 h-2 rounded-full bg-brand-600"></span> Open for voting
            </p>
            <h2 id="live-awards" class="text-ink-900 font-extrabold mt-2" style="font-size:clamp(26px,3.6vw,36px)">
                Awards you can vote in today
            </h2>
            <p class="text-ink-500 text-[15px] mt-2 max-w-xl">
                Pick a campaign, find your nominee and cast your vote in under a minute.
            </p>
        </div>
        <x-ui.btn :href="route('awards.index')" variant="outline" icon-end="arrow-right">Browse all awards</x-ui.btn>
    </div>

    @php $showcase = $liveEvents->isNotEmpty() ? $liveEvents : $recentEvents; @endphp

    @if($showcase->isEmpty())
        <div class="card">
            <x-ui.empty icon="trophy" title="No active awards at the moment"
                        message="New campaigns open regularly. Check the events directory or come back soon.">
                <x-ui.btn :href="route('voting.index')" variant="primary">Awards open for voting</x-ui.btn>
                <x-ui.btn :href="route('admin.register')" variant="outline">Host your own</x-ui.btn>
            </x-ui.empty>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($showcase as $event)
                <x-cards.award :event="$event" />
            @endforeach
        </div>
    @endif
</section>

{{-- ═══ YOUR VOTE. YOUR VOICE. YOUR CHOICE. ═══ --}}
<section class="relative overflow-hidden" style="background:linear-gradient(140deg,#14031f,#3c1f56)"
         aria-labelledby="your-vote">
    <div class="site py-14 sm:py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="eyebrow text-brand-400">Every vote counts</p>
                <h2 id="your-vote" class="text-white font-extrabold mt-3 leading-tight"
                    style="font-size:clamp(28px,4.2vw,44px)">
                    Your Vote. Your Voice.<br><span class="text-brand-500">Your Choice.</span>
                </h2>
                <p class="text-white/65 text-[15.5px] leading-relaxed mt-5 max-w-lg">
                    Support the people and projects you believe in. Votes are recorded the moment your
                    Mobile Money payment is confirmed, and you get a reference for every transaction.
                </p>

                <ol class="mt-8 flex flex-col gap-4">
                    @foreach([
                        ['Discover', 'Browse live awards and their categories.'],
                        ['Select',   'Choose the nominee you want to back.'],
                        ['Vote & pay', 'Enter your number and approve the MoMo prompt.'],
                        ['Confirm',  'Get an instant receipt with your reference.'],
                    ] as $i => [$step, $copy])
                        <li class="flex items-start gap-3.5">
                            <span class="w-8 h-8 rounded-xl bg-brand-600/25 text-brand-300 font-extrabold text-[13px]
                                         flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                            <span>
                                <span class="block text-white font-bold text-[15px]">{{ $step }}</span>
                                <span class="block text-white/55 text-[13.5px] mt-0.5">{{ $copy }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>

                <div class="flex flex-col sm:flex-row gap-3 mt-9">
                    <x-ui.btn :href="route('nominees.index')" variant="primary" icon-end="arrow-right">Find a nominee</x-ui.btn>
                    <x-ui.btn :href="route('events.index')" variant="onDark">See all events</x-ui.btn>
                </div>
            </div>

            {{-- Nominee mosaic, built from real nominee photos where they exist --}}
            @if($spotlight->isNotEmpty())
                <ul class="grid grid-cols-3 gap-3 sm:gap-4" aria-label="Nominees currently up for voting">
                    @foreach($spotlight->take(6) as $i => $nominee)
                        <li class="{{ $i === 0 ? 'col-span-2 row-span-2' : '' }}">
                            <a href="{{ route('nominees.show', $nominee) }}"
                               class="block relative rounded-2xl overflow-hidden group border border-white/10
                                      {{ $i === 0 ? 'aspect-square' : 'aspect-square' }}">
                                @if($nominee->photo_path)
                                    <img src="{{ asset('storage/' . $nominee->photo_path) }}" alt=""
                                         loading="lazy" decoding="async"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <span class="w-full h-full flex items-center justify-center font-display font-extrabold text-white/70"
                                          style="background:linear-gradient(135deg,#5b357b,#241038);font-size:{{ $i === 0 ? '56px' : '26px' }}">
                                        {{ Str::upper(Str::substr($nominee->name, 0, 1)) }}
                                    </span>
                                @endif
                                <span aria-hidden="true" class="absolute inset-0"
                                      style="background:linear-gradient(to top,rgba(20,3,31,.85),transparent 55%)"></span>
                                <span class="absolute inset-x-0 bottom-0 p-3">
                                    <span class="block text-white font-bold leading-tight clamp-2
                                                 {{ $i === 0 ? 'text-[16px]' : 'text-[12px]' }}">{{ $nominee->name }}</span>
                                    <span class="block text-white/55 text-[11px] font-mono mt-0.5">{{ $nominee->code }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</section>

{{-- ═══ TRUST ═══ --}}
<section class="site py-14 sm:py-20" aria-labelledby="trust">
    <div class="text-center max-w-2xl mx-auto mb-10">
        <p class="eyebrow">Why CastVote</p>
        <h2 id="trust" class="text-ink-900 font-extrabold mt-2" style="font-size:clamp(26px,3.6vw,36px)">
            Built so voters and organisers can trust the result
        </h2>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['lock',   'Secure voting',          'Every vote is tied to a verified Paystack payment and recorded once the provider confirms it.'],
            ['cash',   'Multiple payment options', 'Pay with MTN MoMo, Telecel Cash, AirtelTigo Money or a bank card.'],
            ['chart',  'Transparent results',    'Organisers can publish live standings, so voters follow the race as it happens.'],
            ['mobile', 'Accessible voting',      'Vote online or dial the USSD shortcode from any phone — no internet required.'],
        ] as [$icon, $heading, $copy])
            <div class="card p-6">
                <span class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                    <x-ui.icon :name="$icon" :size="20" />
                </span>
                <h3 class="text-[16px] font-extrabold text-ink-900">{{ $heading }}</h3>
                <p class="text-[13.5px] text-ink-500 leading-relaxed mt-2">{{ $copy }}</p>
            </div>
        @endforeach
    </div>

    <p class="text-[12.5px] text-ink-400 text-center mt-6 max-w-2xl mx-auto leading-relaxed">
        Results are published only where the organiser has released them. Vote counts stay private
        until then, and campaigns run as an anonymous tally never expose per-nominee figures.
    </p>
</section>

{{-- ═══ ORGANISER CTA ═══ --}}
<section class="site pb-16 sm:pb-24">
    <div class="rounded-4xl overflow-hidden relative" style="background:linear-gradient(120deg,#c11062,#6f4497)">
        <div aria-hidden="true" class="absolute inset-0 opacity-20"
             style="background-image:radial-gradient(circle,rgba(255,255,255,.6) 1px,transparent 1px);background-size:24px 24px"></div>
        <div class="relative px-6 sm:px-12 py-12 sm:py-16 flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="max-w-xl text-center lg:text-left">
                <h2 class="text-white font-extrabold leading-tight" style="font-size:clamp(24px,3.4vw,34px)">
                    Running an awards show, election or AGM?
                </h2>
                <p class="text-white/75 text-[15px] mt-3 leading-relaxed">
                    Set up categories and nominees, collect votes by web and USSD, track revenue live and
                    settle to Mobile Money — all from one dashboard.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <x-ui.btn :href="route('admin.register')" variant="secondary" size="lg" icon-end="arrow-right">
                    Create an account
                </x-ui.btn>
                <x-ui.btn :href="route('admin.login')" variant="onDark" size="lg">Organiser login</x-ui.btn>
            </div>
        </div>
    </div>
</section>

</x-layouts.public>
