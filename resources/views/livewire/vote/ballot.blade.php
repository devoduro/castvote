<div x-data="{}"
     @open-paystack-popup.window="
        var config = $event.detail.config;
        config.callback = function(response) {
            @this.dispatch('paystack-payment-done', { reference: response.reference });
        };
        config.onClose = function() {
            @this.set('step', 'confirm');
        };
        var handler = PaystackPop.setup(config);
        handler.openIframe();
     ">

    {{-- Category tabs --}}
    <div class="flex gap-2 flex-wrap mb-6">
        @foreach($this->categories as $cat)
        <button wire:click="selectCategory({{ $cat->id }})"
                class="px-4 py-2 rounded-full text-sm font-medium transition
                    {{ $selectedCategoryId == $cat->id
                        ? 'bg-brand-600 text-white shadow-sm'
                        : 'bg-white text-gray-600 border border-gray-200 hover:border-brand-300 hover:text-brand-600' }}">
            {{ $cat->name }}
        </button>
        @endforeach
    </div>

    {{-- STEP: Browse nominees --}}
    @if($step === 'browse')
    <div>
        <h2 class="text-lg font-semibold text-gray-700 mb-4">
            Select a nominee in <span class="text-brand-600">{{ $this->selectedCategory?->name }}</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($this->nominees as $nom)
            <button wire:click="selectNominee({{ $nom->id }})"
                    class="group text-left bg-white hover:bg-brand-50 border border-gray-100 hover:border-brand-300 rounded-2xl p-5 shadow-sm hover:shadow transition">
                <div class="flex items-center gap-4">
                    @if($nom->photo_path)
                    <img src="{{ asset('storage/'.$nom->photo_path) }}"
                         class="w-16 h-16 rounded-full object-cover shrink-0 border-2 border-gray-100 group-hover:border-brand-300 transition">
                    @else
                    <div class="w-16 h-16 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-2xl font-bold shrink-0">
                        {{ strtoupper(substr($nom->name, 0, 1)) }}
                    </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-gray-800 group-hover:text-brand-700 transition truncate">{{ $nom->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Code: <span class="font-mono font-bold text-brand-500">{{ $nom->code }}</span></p>
                        @if($nom->bio)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $nom->bio }}</p>
                        @endif
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-400">Tap to vote</span>
                    <span class="text-brand-600 font-bold text-sm group-hover:translate-x-0.5 transition-transform">Vote →</span>
                </div>
            </button>
            @empty
            <p class="col-span-2 text-center text-gray-400 py-10">No nominees in this category yet.</p>
            @endforelse
        </div>
    </div>
    @endif

    {{-- STEP: Confirm + pay --}}
    @if($step === 'confirm')
    <div class="max-w-md mx-auto">
        <button wire:click="backToBrowse" class="text-sm text-gray-400 hover:text-gray-600 mb-4">← Back</button>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            {{-- Selected nominee card --}}
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                @if($this->selectedNominee?->photo_path)
                <img src="{{ asset('storage/'.$this->selectedNominee->photo_path) }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-brand-200">
                @else
                <div class="w-16 h-16 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-2xl font-bold shrink-0">
                    {{ strtoupper(substr($this->selectedNominee?->name ?? '?', 0, 1)) }}
                </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400">Voting for</p>
                    <p class="font-bold text-xl text-gray-900">{{ $this->selectedNominee?->name }}</p>
                    <p class="text-sm text-gray-500">{{ $this->selectedCategory?->name }}</p>
                </div>
            </div>

            <div class="space-y-4">
                {{-- Quantity (pay-per-vote only) --}}
                @if($event->isPayPerVote())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of votes</label>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('quantity', max(1, $quantity - 1))"
                                class="w-9 h-9 rounded-lg border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">−</button>
                        <input wire:model.live="quantity" type="number" min="1" max="50"
                               class="w-20 text-center border border-gray-300 rounded-lg py-2 text-sm font-mono font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                        <button type="button" wire:click="$set('quantity', min(50, $quantity + 1))"
                                class="w-9 h-9 rounded-lg border border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">+</button>
                        <span class="text-sm text-gray-500">× GHS {{ $event->priceInGhs() }}</span>
                    </div>
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    @if($quantity > 0)
                    <div class="mt-3 bg-brand-50 rounded-xl px-4 py-3 flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total to pay</span>
                        <span class="text-xl font-bold text-brand-700">
                            GHS {{ number_format(($quantity * $event->pricePerVotePesewas()) / 100, 2) }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Phone number --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Your MoMo phone number
                        <span class="text-xs text-gray-400 font-normal">(MTN, Vodafone, AirtelTigo)</span>
                    </label>
                    <input wire:model="phone" type="tel" placeholder="0244 123 456"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-brand-500 outline-none">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">
                        You will receive a MoMo prompt to approve this payment. Your PIN is entered with your network operator — we never see it.
                    </p>
                </div>
                @endif

                {{-- Pay button --}}
                <button wire:click="proceedToPayment"
                        wire:loading.attr="disabled"
                        class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-bold py-3.5 rounded-xl transition text-sm">
                    <span wire:loading.remove>
                        @if($event->isPayPerVote())
                            Pay GHS {{ number_format(($quantity * $event->pricePerVotePesewas()) / 100, 2) }} &amp; Vote
                        @else
                            Cast My Vote →
                        @endif
                    </span>
                    <span wire:loading>Processing…</span>
                </button>

                <p class="text-center text-xs text-gray-400">
                    Secured by Paystack &bull; Payments processed in GHS
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP: Awaiting Paystack popup --}}
    @if($step === 'paying')
    <div class="text-center py-16 max-w-sm mx-auto">
        <div class="text-5xl mb-4 animate-pulse">📱</div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Complete your payment</h2>
        <p class="text-gray-500 text-sm mb-6">
            The Paystack payment window should have opened.<br>
            Complete the payment there to confirm your vote.
        </p>
        <button wire:click="backToBrowse" class="text-sm text-gray-400 hover:text-gray-600 underline">
            Cancel and go back
        </button>
    </div>

    {{-- Listen for Paystack callback and redirect --}}
    <div x-data
         @paystack-payment-done.window="
             window.location = '{{ route('vote.confirmed') }}?ref=' + $event.detail.reference;
         "></div>
    @endif

</div>

{{-- Paystack Inline JS --}}
<script src="https://js.paystack.co/v1/inline.js"></script>
