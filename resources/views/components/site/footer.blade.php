@php
    $contact  = config('castvote.contact');
    $social   = config('castvote.social', []);
    $shortcode = config('castvote.ussd_shortcode');

    $socialIcons = [
        'facebook'  => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z',
        'instagram' => 'M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 5.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zM17.8 6a1.1 1.1 0 100 2.2 1.1 1.1 0 000-2.2z',
        'tiktok'    => 'M16.5 2h-3v13a2.5 2.5 0 11-2.5-2.5c.2 0 .4 0 .5.1V9.5a5.5 5.5 0 102.5 4.6V8.4A6.4 6.4 0 0021 9.6V6.5a3.6 3.6 0 01-3.4-3.4L16.5 2z',
        'x'         => 'M18.24 2.25h3.31l-7.23 8.26 8.5 11.24H16.17l-5.21-6.82-5.97 6.82H1.68l7.73-8.84L1.25 2.25h6.83l4.71 6.23zm-1.16 17.52h1.83L7.08 4.13H5.12z',
    ];
@endphp

<footer class="bg-ink-950 text-ink-300 mt-auto">
    <div class="site pt-14 pb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-8">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <x-ui.logo :size="36" light :href="route('home')" class="mb-4" />
                <p class="text-[13.5px] leading-relaxed text-ink-300/80 max-w-xs">
                    Ghana's platform for award voting, nominations and events. Cast secure votes by web
                    or USSD and pay with Mobile Money.
                </p>
                @if(!empty($social))
                    <ul class="flex items-center gap-2.5 mt-5">
                        @foreach($social as $network => $url)
                            <li>
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                   class="w-9 h-9 rounded-xl bg-white/10 hover:bg-brand-600 text-white flex items-center justify-center transition"
                                   aria-label="CastVote on {{ Str::title($network) }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="{{ $socialIcons[$network] ?? '' }}"/>
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Platform --}}
            <nav aria-labelledby="ft-platform">
                <h2 id="ft-platform" class="text-white font-bold text-[12.5px] uppercase tracking-[.14em] mb-4">Platform</h2>
                <ul class="flex flex-col gap-2.5 text-[13.5px]">
                    <li><a href="{{ route('voting.index') }}" class="hover:text-white transition">Vote now</a></li>
                    <li><a href="{{ route('awards.index') }}" class="hover:text-white transition">Awards</a></li>
                    <li><a href="{{ route('nominees.index') }}" class="hover:text-white transition">Nominees</a></li>
                    <li><a href="{{ route('results.index') }}" class="hover:text-white transition">Results</a></li>
                    <li><a href="{{ route('events.index') }}" class="hover:text-white transition">Events</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">About</a></li>
                </ul>
            </nav>

            {{-- Support --}}
            <nav aria-labelledby="ft-support">
                <h2 id="ft-support" class="text-white font-bold text-[12.5px] uppercase tracking-[.14em] mb-4">Support</h2>
                <ul class="flex flex-col gap-2.5 text-[13.5px]">
                    <li><a href="{{ route('about') }}#faq" class="hover:text-white transition">Help centre</a></li>
                    <li><a href="{{ route('admin.register') }}" class="hover:text-white transition">Become an organiser</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-white transition">Organiser login</a></li>
                    <li><a href="{{ route('vote.privacy') }}" class="hover:text-white transition">Privacy policy</a></li>
                </ul>
                @if($contact['email'] || $contact['phone'])
                    <ul class="flex flex-col gap-2 mt-4 text-[13px] text-ink-300/80">
                        @if($contact['email'])
                            <li class="flex items-center gap-2">
                                <x-ui.icon name="mail" :size="14" class="text-brand-400" />
                                <a href="mailto:{{ $contact['email'] }}" class="hover:text-white transition">{{ $contact['email'] }}</a>
                            </li>
                        @endif
                        @if($contact['phone'])
                            <li class="flex items-center gap-2">
                                <x-ui.icon name="phone" :size="14" class="text-brand-400" />
                                <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}" class="hover:text-white transition">{{ $contact['phone'] }}</a>
                            </li>
                        @endif
                    </ul>
                @endif
            </nav>

            {{-- Voting channels --}}
            <div>
                <h2 class="text-white font-bold text-[12.5px] uppercase tracking-[.14em] mb-4">Ways to vote</h2>
                <div class="rounded-2xl bg-white/[.06] border border-white/10 p-4 mb-4">
                    <p class="text-[11.5px] uppercase tracking-wider text-ink-300/60 font-bold mb-1">Dial on any network</p>
                    <p class="font-mono text-2xl font-extrabold text-brand-400 tracking-wide">{{ $shortcode }}</p>
                    <p class="text-[12px] text-ink-300/60 mt-1">MTN · Telecel · AirtelTigo</p>
                </div>
                <p class="text-[11.5px] uppercase tracking-wider text-ink-300/60 font-bold mb-2">Payments</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['MTN MoMo', 'Telecel Cash', 'AirtelTigo Money', 'Card'] as $method)
                        <span class="badge" style="background:rgba(255,255,255,.09);color:rgba(255,255,255,.8)">{{ $method }}</span>
                    @endforeach
                </div>
                <p class="text-[12px] text-ink-300/60 mt-3 leading-relaxed">Processed securely by Paystack.</p>
            </div>
        </div>

        <div class="border-t border-white/10 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-[12.5px] text-ink-300/60">&copy; {{ date('Y') }} CastVote Ghana. All rights reserved.</p>
            <p class="text-[12.5px] text-ink-300/60">
                Compliant with Ghana's Data Protection Act, 2012 (Act 843)
                @if($contact['city']) <span class="text-ink-300/30">·</span> {{ $contact['city'] }} @endif
            </p>
        </div>
    </div>
</footer>
