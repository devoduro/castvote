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

    {{-- Category grid cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-8">
        @foreach($this->categories as $cat)
        <button wire:click="selectCategory({{ $cat->id }})"
                class="group text-left rounded-2xl border-2 p-4 transition-all duration-200
                    {{ $selectedCategoryId == $cat->id
                        ? 'bg-brand-600 border-brand-600 text-white shadow-lg shadow-brand-600/20'
                        : 'bg-white border-gray-200 hover:border-brand-400 hover:shadow-md text-gray-700' }}">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="font-bold text-sm leading-snug truncate">{{ $cat->name }}</p>
                    <p class="text-xs mt-0.5 {{ $selectedCategoryId == $cat->id ? 'text-brand-200' : 'text-gray-400' }}">
                        {{ $selectedCategoryId == $cat->id ? 'Viewing nominees' : 'Tap to view nominees' }}
                    </p>
                </div>
                <svg class="w-5 h-5 shrink-0 ml-2 {{ $selectedCategoryId == $cat->id ? 'text-white' : 'text-gray-300 group-hover:text-brand-500' }} transition"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </button>
        @endforeach
    </div>

    {{-- STEP: Browse nominees --}}
    @if($step === 'browse')
    <div>
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-200">
            <div class="w-1 h-6 bg-brand-600 rounded-full"></div>
            <h2 class="text-lg font-black text-gray-900">
                {{ $this->selectedCategory?->name }}
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->nominees as $nom)
            <button wire:click="selectNominee({{ $nom->id }})"
                    class="group text-left bg-white hover:bg-brand-50 border-2 border-gray-100 hover:border-brand-400 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-200">
                {{-- Photo --}}
                @if($nom->photo_path)
                <div class="relative overflow-hidden h-44 bg-gray-100">
                    <img src="{{ asset('storage/'.$nom->photo_path) }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                @else
                <div class="h-44 bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center">
                    <span class="text-5xl font-black text-brand-600">{{ strtoupper(substr($nom->name, 0, 1)) }}</span>
                </div>
                @endif

                <div class="p-4">
                    <p class="font-black text-gray-900 group-hover:text-brand-700 transition leading-tight">{{ $nom->name }}</p>
                    <p class="text-xs text-gray-400 mt-1">Code: <span class="font-mono font-bold text-brand-500 bg-brand-50 px-1.5 py-0.5 rounded">{{ $nom->code }}</span></p>
                    @if($nom->bio)
                    <p class="text-xs text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ $nom->bio }}</p>
                    @endif
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">Tap to vote</span>
                        <span class="text-xs font-bold text-white bg-brand-600 group-hover:bg-brand-700 px-3 py-1 rounded-full transition">
                            Vote →
                        </span>
                    </div>
                </div>
            </button>
            @empty
            <div class="col-span-full text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="font-medium">No nominees in this category yet.</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- STEP: Confirm + pay --}}
    @if($step === 'confirm')
    <div class="max-w-lg mx-auto">
        <button wire:click="backToBrowse"
                class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 mb-5 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to nominees
        </button>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Selected nominee banner --}}
            <div class="bg-gradient-to-br from-brand-700 to-brand-900 p-6 flex items-center gap-5">
                @if($this->selectedNominee?->photo_path)
                <img src="{{ asset('storage/'.$this->selectedNominee->photo_path) }}"
                     class="w-20 h-20 rounded-2xl object-cover border-2 border-white/30 shrink-0 shadow-lg">
                @else
                <div class="w-20 h-20 rounded-2xl bg-white/20 text-white flex items-center justify-center text-3xl font-black shrink-0">
                    {{ strtoupper(substr($this->selectedNominee?->name ?? '?', 0, 1)) }}
                </div>
                @endif
                <div>
                    <p class="text-brand-200 text-xs font-semibold uppercase tracking-wider">Voting for</p>
                    <p class="font-black text-2xl text-white leading-tight mt-0.5">{{ $this->selectedNominee?->name }}</p>
                    <p class="text-brand-200 text-sm mt-1">{{ $this->selectedCategory?->name }}</p>
                </div>
            </div>

            <div class="p-6 space-y-5">
                {{-- Quantity (pay-per-vote only) --}}
                @if($event->isPayPerVote())
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Number of Votes</label>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('quantity', max(1, $quantity - 1))"
                                class="w-10 h-10 rounded-xl border-2 border-gray-200 text-gray-600 font-black text-lg hover:border-brand-400 hover:text-brand-600 transition flex items-center justify-center">−</button>
                        <input wire:model.live="quantity" type="number" min="1" max="50"
                               class="w-20 text-center border-2 border-gray-200 rounded-xl py-2 text-lg font-black focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                        <button type="button" wire:click="$set('quantity', min(50, $quantity + 1))"
                                class="w-10 h-10 rounded-xl border-2 border-gray-200 text-gray-600 font-black text-lg hover:border-brand-400 hover:text-brand-600 transition flex items-center justify-center">+</button>
                        <span class="text-sm text-gray-400">× GHS {{ $event->priceInGhs() }}</span>
                    </div>
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    @if($quantity > 0)
                    <div class="mt-4 bg-brand-50 border border-brand-200 rounded-2xl px-5 py-4 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-600">Total to pay</span>
                        <span class="text-2xl font-black text-brand-700">
                            GHS {{ number_format(($quantity * $event->pricePerVotePesewas()) / 100, 2) }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Phone number --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        MoMo Phone Number
                        <span class="text-xs text-gray-400 font-normal ml-1">(MTN, Vodafone, AirtelTigo)</span>
                    </label>
                    <input wire:model="phone" type="tel" placeholder="0244 123 456"
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-base font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                        You'll receive a MoMo prompt on your phone. Your PIN is entered with your network — we never see it.
                    </p>
                </div>
                @endif

                {{-- Pay / Vote button --}}
                <button wire:click="proceedToPayment"
                        wire:loading.attr="disabled"
                        class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-black py-4 rounded-2xl transition text-base shadow-lg shadow-brand-600/30 flex items-center justify-center gap-2">
                    <span wire:loading.remove>
                        @if($event->isPayPerVote())
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Pay GHS {{ number_format(($quantity * $event->pricePerVotePesewas()) / 100, 2) }} &amp; Vote
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Cast My Vote
                        @endif
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Processing…
                    </span>
                </button>

                <div class="flex items-center justify-center gap-2 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Secured by Paystack &bull; Payments processed in GHS
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP: Awaiting Paystack popup --}}
    @if($step === 'paying')
    <div class="text-center py-20 max-w-sm mx-auto">
        <div class="w-20 h-20 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-brand-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-gray-900 mb-2">Complete Your Payment</h2>
        <p class="text-gray-500 text-sm leading-relaxed mb-6">
            The Paystack payment window should have opened.<br>
            Approve the MoMo prompt on your phone to confirm your vote.
        </p>
        <button wire:click="backToBrowse" class="text-sm text-gray-400 hover:text-gray-700 underline underline-offset-2 transition">
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
