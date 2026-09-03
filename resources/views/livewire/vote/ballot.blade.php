<div x-data
     @open-paystack-popup.window="
        const config = $event.detail.config;
        config.callback = response => @this.dispatch('paystack-payment-done', { reference: response.reference });
        config.onClose  = () => @this.set('step', 'confirm');
        PaystackPop.setup(config).openIframe();
     ">

    {{-- Progress --}}
    <x-ui.steps :current="$step === 'browse' ? 1 : ($step === 'confirm' ? 1 : 2)" class="mb-7" />

    {{-- ═══ Categories ═══ --}}
    <section aria-labelledby="ballot-cats" class="mb-8">
        <h2 id="ballot-cats" class="text-[13px] font-bold uppercase tracking-wider text-ink-400 mb-3">
            Choose a category
        </h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" role="tablist">
            @foreach($this->categories as $cat)
                @php $active = $selectedCategoryId == $cat->id; @endphp
                <button type="button" wire:click="selectCategory({{ $cat->id }})" wire:key="cat-{{ $cat->id }}"
                        role="tab" aria-selected="{{ $active ? 'true' : 'false' }}"
                        class="group text-left rounded-2xl border-[1.5px] p-4 transition-all
                            {{ $active
                                ? 'bg-brand-600 border-brand-600 text-white shadow-brand'
                                : 'bg-white border-ink-100 text-ink-700 hover:border-brand-300 hover:shadow-card' }}">
                    <span class="flex items-center justify-between gap-2">
                        <span class="min-w-0">
                            <span class="block font-bold text-[14px] leading-snug clamp-2">{{ $cat->name }}</span>
                            <span class="block text-[12px] mt-0.5 {{ $active ? 'text-white/70' : 'text-ink-400' }}">
                                {{ $active ? 'Viewing nominees' : 'Tap to view nominees' }}
                            </span>
                        </span>
                        <x-ui.icon name="chevron-right" :size="17"
                                   class="shrink-0 {{ $active ? 'text-white' : 'text-ink-300 group-hover:text-brand-500' }} transition" />
                    </span>
                </button>
            @endforeach
        </div>
    </section>

    {{-- ═══ STEP: browse nominees ═══ --}}
    @if($step === 'browse')
        <section aria-labelledby="ballot-noms">
            <div class="flex items-center gap-3 mb-5 pb-4 border-b border-ink-100">
                <span aria-hidden="true" class="w-1 h-6 rounded-full bg-brand-600"></span>
                <h2 id="ballot-noms" class="text-[18px] font-extrabold text-ink-900">
                    {{ $this->selectedCategory?->name ?? 'Nominees' }}
                </h2>
            </div>

            <div wire:loading.class="opacity-50" wire:target="selectCategory"
                 class="grid gap-4 grid-cols-2 lg:grid-cols-3 transition-opacity">
                @forelse($this->nominees as $nom)
                    <button type="button" wire:click="selectNominee({{ $nom->id }})" wire:key="nom-{{ $nom->id }}"
                            class="card card-hover overflow-hidden text-left flex flex-col group">
                        <span class="ratio-4x3 block">
                            @if($nom->photo_path)
                                <img src="{{ asset('storage/' . $nom->photo_path) }}" alt="" loading="lazy" decoding="async"
                                     class="transition-transform duration-500 group-hover:scale-105">
                            @else
                                <span class="w-full h-full flex items-center justify-center font-display font-extrabold text-brand-400"
                                      style="background:linear-gradient(135deg,#ffe4ef,#efeaf6);font-size:40px">
                                    {{ Str::upper(Str::substr($nom->name, 0, 1)) }}
                                </span>
                            @endif
                        </span>

                        <span class="p-4 flex flex-col gap-1.5 flex-1">
                            <span class="block font-extrabold text-ink-900 text-[15px] leading-snug clamp-2
                                         group-hover:text-brand-700 transition">{{ $nom->name }}</span>
                            <span class="block text-[12px] text-ink-400">
                                Code <span class="font-mono font-bold text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded">{{ $nom->code }}</span>
                            </span>
                            @if($nom->bio)
                                <span class="block text-[12.5px] text-ink-500 leading-relaxed clamp-2">{{ $nom->bio }}</span>
                            @endif
                            <span class="btn btn-primary btn-sm btn-block mt-auto pointer-events-none">
                                Vote <x-ui.icon name="arrow-right" :size="14" />
                            </span>
                        </span>
                    </button>
                @empty
                    <div class="col-span-full card">
                        <x-ui.empty icon="users" title="No nominees in this category yet"
                                    message="The organiser has not added nominees here. Try another category." />
                    </div>
                @endforelse
            </div>
        </section>
    @endif

    {{-- ═══ STEP: confirm and pay ═══ --}}
    @if($step === 'confirm')
        <section class="max-w-xl" aria-labelledby="ballot-confirm">
            <button type="button" wire:click="backToBrowse" class="btn btn-ghost btn-sm mb-4">
                <x-ui.icon name="arrow-left" :size="15" /> Back to nominees
            </button>

            <div class="card overflow-hidden">
                {{-- Selected nominee --}}
                <div class="p-5 sm:p-6 flex items-center gap-4" style="background:linear-gradient(135deg,#c11062,#5b357b)">
                    @if($this->selectedNominee?->photo_path)
                        <img src="{{ asset('storage/' . $this->selectedNominee->photo_path) }}" alt=""
                             class="w-[72px] h-[72px] rounded-2xl object-cover border-2 border-white/25 shrink-0">
                    @else
                        <span class="w-[72px] h-[72px] rounded-2xl bg-white/20 text-white font-display font-extrabold
                                     text-3xl flex items-center justify-center shrink-0" aria-hidden="true">
                            {{ Str::upper(Str::substr($this->selectedNominee?->name ?? '?', 0, 1)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="text-white/65 text-[11.5px] font-bold uppercase tracking-wider">Voting for</p>
                        <h2 id="ballot-confirm" class="text-white font-extrabold text-[21px] leading-tight mt-1 clamp-2">
                            {{ $this->selectedNominee?->name }}
                        </h2>
                        <p class="text-white/70 text-[13px] mt-1">{{ $this->selectedCategory?->name }}</p>
                    </div>
                </div>

                <div class="p-5 sm:p-6 flex flex-col gap-5">
                    @if($event->isPayPerVote())
                        {{-- Quantity --}}
                        <div>
                            <label for="ballot-qty" class="label">Number of votes</label>
                            <div class="flex items-center gap-3">
                                <button type="button" wire:click="decrement" aria-label="Remove one vote"
                                        class="w-11 h-11 rounded-xl border-[1.5px] border-ink-100 text-ink-600 flex items-center
                                               justify-center hover:border-brand-400 hover:text-brand-600 transition
                                               disabled:opacity-40" @disabled($quantity <= 1)>
                                    <x-ui.icon name="minus" :size="17" :stroke="2.6" />
                                </button>
                                <input id="ballot-qty" wire:model.live.debounce.400ms="quantity" type="number"
                                       inputmode="numeric" min="1" max="{{ \App\Livewire\Vote\Ballot::MAX_QUANTITY }}"
                                       class="input text-center font-extrabold text-[18px]" style="width:88px"
                                       aria-describedby="ballot-qty-hint">
                                <button type="button" wire:click="increment" aria-label="Add one vote"
                                        class="w-11 h-11 rounded-xl border-[1.5px] border-ink-100 text-ink-600 flex items-center
                                               justify-center hover:border-brand-400 hover:text-brand-600 transition
                                               disabled:opacity-40" @disabled($quantity >= \App\Livewire\Vote\Ballot::MAX_QUANTITY)>
                                    <x-ui.icon name="plus" :size="17" :stroke="2.6" />
                                </button>
                                <span class="text-[13.5px] text-ink-400">× GH&#8373;{{ $event->priceInGhs() }}</span>
                            </div>
                            @error('quantity')
                                <p class="error-msg" role="alert"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                            @else
                                <p id="ballot-qty-hint" class="hint">
                                    Up to {{ \App\Livewire\Vote\Ballot::MAX_QUANTITY }} votes per transaction.
                                </p>
                            @enderror
                        </div>

                        <div class="rounded-2xl bg-brand-50 border border-brand-100 px-5 py-4 flex items-center justify-between gap-4">
                            <span class="text-[14px] font-semibold text-ink-600">Total to pay</span>
                            <span class="font-display text-[26px] font-extrabold text-brand-700 leading-none" aria-live="polite">
                                GH&#8373;{{ number_format(($quantity * $event->pricePerVotePesewas()) / 100, 2) }}
                            </span>
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="ballot-momo" class="label">Mobile Money number</label>
                            <input id="ballot-momo" wire:model.blur="phone" type="tel" inputmode="tel" autocomplete="tel"
                                   placeholder="024 123 4567"
                                   class="input font-mono @error('phone') is-error @enderror"
                                   @error('phone') aria-invalid="true" aria-describedby="ballot-momo-err" @enderror>
                            @error('phone')
                                <p id="ballot-momo-err" class="error-msg" role="alert">
                                    <x-ui.icon name="warning" :size="14" /> {{ $message }}
                                </p>
                            @else
                                <p class="hint">MTN, Telecel or AirtelTigo. You enter your PIN with your network — we never see it.</p>
                            @enderror
                        </div>
                    @endif

                    <x-ui.btn type="button" wire:click="proceedToPayment" loading="proceedToPayment"
                              variant="primary" size="lg" block
                              icon="{{ $event->isPayPerVote() ? 'lock' : 'check-circle' }}">
                        @if($event->isPayPerVote())
                            Pay GH&#8373;{{ number_format(($quantity * $event->pricePerVotePesewas()) / 100, 2) }} &amp; vote
                        @else
                            Cast my vote
                        @endif
                    </x-ui.btn>

                    <p class="flex items-center justify-center gap-2 text-[12.5px] text-ink-400">
                        <x-ui.icon name="shield" :size="14" class="text-green-600" />
                        Secured by Paystack · Payments processed in GH&#8373;
                    </p>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ STEP: awaiting Paystack ═══ --}}
    @if($step === 'paying')
        <section class="card p-8 sm:p-12 text-center max-w-xl">
            <div class="w-16 h-16 rounded-full bg-brand-50 flex items-center justify-center mx-auto mb-5">
                <span class="btn-spin" style="width:26px;height:26px;border-color:#fecce0;border-top-color:#e11d74"></span>
            </div>
            <h2 class="text-[21px] font-extrabold text-ink-900">Processing your vote…</h2>
            <p class="text-[14.5px] text-ink-500 leading-relaxed mt-2.5 max-w-sm mx-auto">
                The Paystack window should be open. Approve the Mobile Money prompt on your phone to confirm your vote.
            </p>
            <button type="button" wire:click="backToBrowse" class="btn btn-ghost btn-sm mt-6">Cancel and go back</button>
        </section>

        <div x-data @paystack-payment-done.window="
                window.location = '{{ route('vote.confirmed') }}?ref=' + $event.detail.reference;
             "></div>
    @endif

    {{-- Paystack Inline --}}
    <script src="https://js.paystack.co/v1/inline.js"></script>
</div>
