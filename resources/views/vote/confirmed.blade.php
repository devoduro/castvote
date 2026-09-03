<x-layouts.public>
<x-slot name="title">Vote Confirmed</x-slot>

@php
    // Falls back to the session values flashed by the payment callback when the
    // reference is not on the URL.
    $reference  = $payment?->provider_reference ?? session('reference');
    $nomineeName = $nominee?->name              ?? session('nominee_name');
    $categoryName = $nominee?->category?->name  ?? session('category_name');
    $quantity   = $payment->metadata['quantity'] ?? session('quantity', 1);
    $amountGhs  = $payment?->amountInGhs()      ?? session('amount_ghs');
    $eventSlug  = $payment?->event?->slug       ?? session('event_slug');
    $paid       = $payment?->isSuccess();
    $when       = $payment?->created_at ?? now();
@endphp

<div class="site py-10 sm:py-16 max-w-2xl">

    <x-ui.steps :current="3" class="mb-9 justify-center" />

    <div class="text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6
                    {{ $paid === false ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600' }}">
            <x-ui.icon :name="$paid === false ? 'clock' : 'check'" :size="40" :stroke="2.6" />
        </div>

        <h1 class="text-ink-900 font-extrabold leading-tight" style="font-size:clamp(28px,4.4vw,40px)">
            {{ $paid === false ? 'Payment processing' : 'Vote Successful!' }}
        </h1>
        <p class="text-[15.5px] text-ink-500 leading-relaxed mt-3 max-w-md mx-auto">
            @if($paid === false)
                Your payment is still being confirmed by the provider. Your vote is credited automatically
                the moment it clears — usually within two minutes.
            @else
                Your vote has been recorded successfully.
            @endif
        </p>
    </div>

    {{-- ── Receipt ── --}}
    @if($reference)
        <div class="card overflow-hidden mt-9">
            <div class="px-5 sm:px-7 py-4 border-b border-ink-100 flex items-center justify-between gap-3"
                 style="background:linear-gradient(135deg,#fff1f7,#f7f5fb)">
                <h2 class="text-[15px] font-extrabold text-ink-900">Transaction details</h2>
                @if($paid === false)
                    <x-ui.badge tone="warning" dot>Pending confirmation</x-ui.badge>
                @elseif($paid)
                    <x-ui.badge tone="success">Confirmed</x-ui.badge>
                @endif
            </div>

            <dl class="divide-y divide-ink-100">
                @foreach(array_filter([
                    ['Nominee',       $nomineeName],
                    ['Category',      $categoryName],
                    ['Award',         $payment?->event?->name],
                    ['Number of votes', $quantity ? number_format((int) $quantity) : null],
                    ['Amount paid',   $amountGhs ? 'GH₵' . $amountGhs : null],
                    ['Payment method', $payment?->momo_network ? Str::title($payment->momo_network) . ' Mobile Money' : null],
                    ['Date & time',   $when->format('d M Y, g:ia')],
                ], fn ($row) => filled($row[1])) as [$label, $value])
                    <div class="flex items-baseline justify-between gap-6 px-5 sm:px-7 py-3.5">
                        <dt class="text-[13.5px] text-ink-500 shrink-0">{{ $label }}</dt>
                        <dd class="text-[14.5px] font-bold text-ink-900 text-right">{{ $value }}</dd>
                    </div>
                @endforeach

                <div class="flex items-baseline justify-between gap-6 px-5 sm:px-7 py-3.5">
                    <dt class="text-[13.5px] text-ink-500 shrink-0">Reference</dt>
                    <dd class="text-[12.5px] font-mono font-semibold text-ink-700 bg-ink-50 px-2.5 py-1 rounded-lg break-all text-right">
                        {{ $reference }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- ── Actions ── --}}
        <div class="grid sm:grid-cols-2 gap-3 mt-5">
            <x-ui.btn :href="route('vote.receipt', $reference)" variant="outline" block icon="download">
                Download receipt
            </x-ui.btn>
            <x-ui.btn type="button" variant="outline" block icon="share"
                      data-share-url="{{ $nominee ? route('nominees.show', $nominee) : route('home') }}"
                      data-share-text="{{ $nomineeName ? 'I just voted for ' . $nomineeName . ' on CastVote!' : 'Vote on CastVote' }}">
                Share
            </x-ui.btn>
        </div>
    @endif

    {{-- ── Next steps ── --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-8">
        @if($eventSlug)
            <x-ui.btn :href="route('vote.event', $eventSlug)" variant="primary" icon-end="arrow-right">Vote again</x-ui.btn>
        @endif
        @if($payment?->event?->resultsArePublic() && ! $payment->event->isAnonymousTally())
            <x-ui.btn :href="route('results.show', $payment->event->slug)" variant="secondary" icon="chart">View results</x-ui.btn>
        @endif
        <x-ui.btn :href="route('awards.index')" variant="ghost">Browse awards</x-ui.btn>
    </div>

    <p class="text-[12.5px] text-ink-400 text-center mt-8 leading-relaxed">
        Keep your reference number in case of a dispute. Votes are final and cannot be reversed.
    </p>
</div>

<x-slot name="scripts">
<script>
    document.querySelectorAll('[data-share-url]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const url  = btn.dataset.shareUrl;
            const text = btn.dataset.shareText;
            if (navigator.share) {
                try { await navigator.share({ title: 'CastVote', text: text, url: url }); return; } catch (e) { return; }
            }
            try {
                await navigator.clipboard.writeText(text + ' ' + url);
                window.dispatchEvent(new CustomEvent('cv-toast', {
                    detail: { type: 'success', message: 'Link copied — paste it anywhere to share.' },
                }));
            } catch (e) {
                window.dispatchEvent(new CustomEvent('cv-toast', {
                    detail: { type: 'error', message: 'Sharing is not available in this browser.' },
                }));
            }
        });
    });
</script>
</x-slot>

</x-layouts.public>
