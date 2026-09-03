@props([
    'current' => 1,
    'steps'   => ['Vote', 'Payment', 'Confirmation'],
])

<ol {{ $attributes->merge(['class' => 'flex items-center gap-2 sm:gap-3']) }}
    aria-label="Voting progress">
    @foreach($steps as $i => $step)
        @php
            $n    = $i + 1;
            $done = $n < $current;
            $now  = $n === $current;
        @endphp
        <li class="flex items-center gap-2 sm:gap-3 {{ $n === count($steps) ? '' : 'flex-1' }}"
            @if($now) aria-current="step" @endif>
            <span class="flex items-center gap-2 shrink-0">
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-[12px] font-extrabold shrink-0
                    {{ $done ? 'bg-brand-600 text-white'
                       : ($now ? 'bg-brand-600 text-white ring-4 ring-brand-100'
                       : 'bg-ink-100 text-ink-400') }}">
                    @if($done)
                        <x-ui.icon name="check" :size="13" :stroke="3" />
                    @else
                        {{ $n }}
                    @endif
                </span>
                <span class="text-[12.5px] font-bold {{ $now || $done ? 'text-ink-900' : 'text-ink-400' }} hidden sm:inline">
                    {{ $step }}
                </span>
            </span>
            @if($n !== count($steps))
                <span class="h-[2px] flex-1 rounded-full {{ $done ? 'bg-brand-600' : 'bg-ink-100' }}"></span>
            @endif
        </li>
    @endforeach
</ol>
