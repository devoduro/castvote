<div id="vote" class="card overflow-hidden scroll-mt-24"
     x-data
     @open-paystack-popup.window="
        const config = $event.detail.config;
        config.callback = response => @this.dispatch('paystack-payment-done', { reference: response.reference });
        config.onClose  = () => @this.call('backToForm');
        PaystackPop.setup(config).openIframe();
     ">

    {{-- Header --}}
    <div class="px-5 sm:px-6 pt-5 sm:pt-6 pb-5 border-b border-ink-100"
         style="background:linear-gradient(135deg,#fff1f7,#f7f5fb)">
        <p class="eyebrow">Cast your vote</p>
        <h2 class="text-[20px] sm:text-[22px] font-extrabold text-ink-900 mt-1.5 leading-snug">
            Vote for {{ $nominee->name }}
        </h2>
        <p class="text-[13.5px] text-ink-500 mt-1">{{ $category->name }} · {{ $event->name }}</p>

        <x-ui.steps :current="$step === 'paying' ? 2 : 1" class="mt-5" />
    </div>

    @if($step === 'form')
        <div class="p-5 sm:p-6 flex flex-col gap-5">

            @error('quantity')
                <p class="error-msg" role="alert">
                    <x-ui.icon name="warning" :size="14" /> {{ $message }}
                </p>
            @enderror

            @if($event->isPayPerVote())
                {{-- Quantity --}}
                <div>
                    <label for="qty" class="label">Number of votes</label>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="decrement" aria-label="Remove one vote"
                                class="w-11 h-11 rounded-xl border-[1.5px] border-ink-100 text-ink-600 flex items-center justify-center
                                       hover:border-brand-400 hover:text-brand-600 transition disabled:opacity-40"
                                @disabled($quantity <= 1)>
                            <x-ui.icon name="minus" :size="17" :stroke="2.6" />
                        </button>

                        <input id="qty" wire:model.live.debounce.400ms="quantity" type="number" inputmode="numeric"
                               min="1" max="{{ $this->maxQuantity() }}"
                               class="input text-center font-extrabold text-[18px]" style="width:88px"
                               aria-describedby="qty-hint">

                        <button type="button" wire:click="increment" aria-label="Add one vote"
                                class="w-11 h-11 rounded-xl border-[1.5px] border-ink-100 text-ink-600 flex items-center justify-center
                                       hover:border-brand-400 hover:text-brand-600 transition disabled:opacity-40"
                                @disabled($quantity >= $this->maxQuantity())>
                            <x-ui.icon name="plus" :size="17" :stroke="2.6" />
                        </button>

                        <span class="text-[13.5px] text-ink-400">× GH&#8373;{{ $event->priceInGhs() }}</span>
                    </div>

                    {{-- Quick amounts --}}
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach([5, 10, 50, 100] as $preset)
                            @continue($preset > $this->maxQuantity())
                            <button type="button" wire:click="setQuantity({{ $preset }})"
                                    class="btn btn-sm {{ (int) $quantity === $preset ? 'btn-primary' : 'btn-outline' }}">
                                {{ $preset }} votes
                            </button>
                        @endforeach
                    </div>
                    <p id="qty-hint" class="hint">Up to {{ $this->maxQuantity() }} votes per transaction.</p>
                </div>

                {{-- Running total --}}
                <div class="rounded-2xl bg-brand-50 border border-brand-100 px-5 py-4 flex items-center justify-between gap-4">
                    <span class="text-[14px] font-semibold text-ink-600">Amount to pay</span>
                    <span class="font-display text-[26px] font-extrabold text-brand-700 leading-none"
                          aria-live="polite">GH&#8373;{{ $this->amountGhs() }}</span>
                </div>

                {{-- Phone --}}
                <div>
                    <label for="momo" class="label">Mobile Money number</label>
                    <input id="momo" wire:model.blur="phone" type="tel" inputmode="tel" autocomplete="tel"
                           placeholder="024 123 4567" class="input font-mono @error('phone') is-error @enderror"
                           @error('phone') aria-invalid="true" aria-describedby="momo-err" @enderror>
                    @error('phone')
                        <p id="momo-err" class="error-msg" role="alert">
                            <x-ui.icon name="warning" :size="14" /> {{ $message }}
                        </p>
                    @else
                        <p class="hint">MTN, Telecel or AirtelTigo. You approve the prompt on your phone — we never see your PIN.</p>
                    @enderror
                </div>
            @else
                <div class="rounded-2xl bg-green-50 border border-green-100 px-5 py-4 flex items-center gap-3">
                    <x-ui.icon name="check-circle" :size="20" class="text-green-700" />
                    <p class="text-[14px] font-semibold text-green-800">This campaign is free to vote in.</p>
                </div>
            @endif

            <x-ui.btn type="button" wire:click="proceedToPayment" loading="proceedToPayment"
                      variant="primary" size="lg" block icon="{{ $event->isPayPerVote() ? 'lock' : 'check-circle' }}">
                @if($event->isPayPerVote())
                    Proceed to payment · GH&#8373;{{ $this->amountGhs() }}
                @else
                    Cast my vote
                @endif
            </x-ui.btn>

            <p class="flex items-center justify-center gap-2 text-[12.5px] text-ink-400 text-center">
                <x-ui.icon name="shield" :size="14" class="text-green-600" />
                Secured by Paystack. Votes are final and cannot be reversed.
            </p>
        </div>
    @else
        {{-- Awaiting the Paystack popup --}}
        <div class="p-6 sm:p-10 text-center">
            <div class="w-16 h-16 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-5">
                <span class="btn-spin" style="width:26px;height:26px;border-color:#fecce0;border-top-color:#e11d74"></span>
            </div>
            <h3 class="text-[19px] font-extrabold text-ink-900">Processing your vote…</h3>
            <p class="text-[14px] text-ink-500 leading-relaxed mt-2 max-w-sm mx-auto">
                The Paystack window should be open. Approve the Mobile Money prompt on
                <span class="font-semibold text-ink-700">{{ $phone }}</span> to confirm
                {{ $quantity }} {{ Str::plural('vote', $quantity) }} for {{ $nominee->name }}.
            </p>
            <button type="button" wire:click="backToForm"
                    class="btn btn-ghost btn-sm mt-6">Cancel and go back</button>
        </div>

        <div x-data @paystack-payment-done.window="
                window.location = '{{ route('vote.confirmed') }}?ref=' + $event.detail.reference;
             "></div>
    @endif

    {{-- Paystack Inline — the same integration the category ballot uses. --}}
    <script src="https://js.paystack.co/v1/inline.js"></script>
</div>
