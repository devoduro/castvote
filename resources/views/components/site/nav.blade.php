@php
    $links = [
        ['label' => 'Home',     'href' => route('home'),           'active' => request()->routeIs('home')],
        ['label' => 'Awards',   'href' => route('awards.index'),   'active' => request()->routeIs('awards.*') || request()->routeIs('vote.index')],
        ['label' => 'Voting',   'href' => route('voting.index'),   'active' => request()->routeIs('voting.*') || request()->routeIs('vote.event')],
        ['label' => 'Nominees', 'href' => route('nominees.index'), 'active' => request()->routeIs('nominees.*')],
        ['label' => 'Results',  'href' => route('results.index'),  'active' => request()->routeIs('results.*')],
        ['label' => 'Events',   'href' => route('events.index'),   'active' => request()->routeIs('events.*')],
        ['label' => 'About',    'href' => route('about'),          'active' => request()->routeIs('about')],
    ];
@endphp

<header x-data="{ open: false }" @keydown.escape.window="open = false"
        class="bg-white/95 backdrop-blur border-b border-ink-100 sticky top-0 z-50">
    <nav class="site" aria-label="Main">
        <div class="flex items-center justify-between h-16 gap-4">

            <x-ui.logo :size="34" :href="route('home')" />

            {{-- Desktop links --}}
            <ul class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                @foreach($links as $link)
                    <li>
                        <a href="{{ $link['href'] }}"
                           @if($link['active']) aria-current="page" @endif
                           class="inline-flex items-center h-9 px-3.5 rounded-lg text-[14px] font-semibold transition
                                  {{ $link['active']
                                     ? 'text-brand-700 bg-brand-50'
                                     : 'text-ink-600 hover:text-brand-700 hover:bg-brand-50/70' }}">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Desktop actions --}}
            <div class="hidden lg:flex items-center gap-2 shrink-0">
                @auth('admin')
                    <x-ui.btn :href="route('admin.dashboard')" variant="outline" size="sm">Dashboard</x-ui.btn>
                @else
                    <a href="{{ route('admin.login') }}"
                       class="inline-flex items-center h-9 px-3 rounded-lg text-[14px] font-semibold text-ink-600 hover:text-ink-900 hover:bg-ink-50 transition">
                        Login
                    </a>
                    <x-ui.btn :href="route('admin.register')" variant="outline" size="sm">Register</x-ui.btn>
                @endauth
                <x-ui.btn :href="route('voting.index')" variant="primary" size="sm" icon-end="arrow-right">
                    Vote Now
                </x-ui.btn>
            </div>

            {{-- Mobile toggle --}}
            <button type="button" @click="open = !open"
                    class="lg:hidden w-11 h-11 -mr-2 rounded-xl text-ink-600 hover:bg-ink-50 flex items-center justify-center transition"
                    :aria-expanded="open.toString()" aria-controls="cv-mobile-nav" aria-label="Toggle navigation menu">
                <x-ui.icon x-show="!open" name="menu" :size="22" />
                <x-ui.icon x-show="open" name="close" :size="22" x-cloak />
            </button>
        </div>

        {{-- Mobile menu --}}
        <div id="cv-mobile-nav" x-show="open" x-cloak x-collapse class="lg:hidden pb-4">
            <ul class="flex flex-col gap-1 pt-2 border-t border-ink-100">
                @foreach($links as $link)
                    <li>
                        <a href="{{ $link['href'] }}"
                           @if($link['active']) aria-current="page" @endif
                           class="flex items-center h-12 px-3 rounded-xl text-[15px] font-semibold transition
                                  {{ $link['active'] ? 'text-brand-700 bg-brand-50' : 'text-ink-700 hover:bg-ink-50' }}">
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="flex flex-col gap-2 mt-3 pt-3 border-t border-ink-100">
                <x-ui.btn :href="route('voting.index')" variant="primary" block icon-end="arrow-right">Vote Now</x-ui.btn>
                @auth('admin')
                    <x-ui.btn :href="route('admin.dashboard')" variant="outline" block>Organiser Dashboard</x-ui.btn>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <x-ui.btn :href="route('admin.login')" variant="outline">Login</x-ui.btn>
                        <x-ui.btn :href="route('admin.register')" variant="outline">Register</x-ui.btn>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>
