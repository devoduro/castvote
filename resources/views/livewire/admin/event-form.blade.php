<div class="max-w-2xl space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            {{ $event?->exists ? 'Edit Event' : 'Create New Event' }}
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            {{ $event?->exists ? 'Update the event settings below.' : 'Fill in the details to launch a new voting event.' }}
        </p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4">
        <p class="text-sm font-bold text-red-700 mb-1">Please fix the following errors:</p>
        <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-slate-100 divide-y divide-slate-100">

        {{-- ── Section: Basic Info ── --}}
        <div class="p-6 space-y-5">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Event Details</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Event Name <span class="text-red-500">*</span>
                </label>
                <input wire:model="name" type="text" placeholder="e.g. Ghana Music Awards 2025"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition">
                @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Event Type <span class="text-red-500">*</span></label>
                    <select wire:model.live="event_type"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition bg-white">
                        <option value="award">🏆 Award Show</option>
                        <option value="agm">🏢 Corporate AGM</option>
                        <option value="election">🗳️ Student Election</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                    <select wire:model="status"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition bg-white">
                        <option value="draft">Draft</option>
                        <option value="live">Live</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Starts At</label>
                    <input wire:model="starts_at" type="datetime-local"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ends At</label>
                    <input wire:model="ends_at" type="datetime-local"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">USSD Shortcode</label>
                    <input wire:model="ussd_shortcode" type="text" placeholder="*928*24#"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Arkesel Service ID</label>
                    <input wire:model="ussd_short_id" type="text" placeholder="240"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-brand-500 outline-none transition">
                </div>
            </div>
        </div>

        {{-- ── Section: Event Flyer ── --}}
        <div class="p-6 space-y-4">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Event Flyer</h2>
            <p class="text-xs text-slate-400">Displayed on the public voting site. Recommended: 1200×630px, PNG or JPG, max 2 MB.</p>

            {{-- Current / preview --}}
            @if($existingFlyerPath && !$flyer)
            <div class="flex items-start gap-4">
                <img src="{{ asset('storage/' . $existingFlyerPath) }}" alt="Current flyer"
                     class="h-36 w-auto rounded-xl border border-slate-200 object-cover shadow-sm">
                <div class="text-xs text-slate-500 mt-1">
                    <p class="font-semibold text-slate-700 mb-0.5">Current flyer</p>
                    <p>Upload a new image below to replace it.</p>
                </div>
            </div>
            @endif

            @if($flyer)
            <div class="flex items-start gap-4">
                <img src="{{ $flyer->temporaryUrl() }}" alt="New flyer preview"
                     class="h-36 w-auto rounded-xl border-2 border-brand-400 object-cover shadow-sm">
                <div class="text-xs mt-1">
                    <p class="font-semibold text-brand-700 mb-0.5">New flyer selected</p>
                    <p class="text-slate-400">Will be saved when you click Save.</p>
                </div>
            </div>
            @endif

            {{-- Upload dropzone --}}
            <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-slate-200 rounded-xl p-8 cursor-pointer hover:border-brand-400 hover:bg-brand-50/40 transition group">
                <svg class="w-8 h-8 text-slate-400 group-hover:text-brand-500 mb-2 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                <p class="text-sm font-medium text-slate-600 group-hover:text-brand-700 transition">
                    {{ ($existingFlyerPath && !$flyer) ? 'Click to replace flyer' : 'Click to upload event flyer' }}
                </p>
                <p class="text-xs text-slate-400 mt-1">PNG, JPG, WEBP — max 2 MB</p>
                <input wire:model="flyer" type="file" accept="image/*" class="hidden">
            </label>

            <div wire:loading wire:target="flyer" class="flex items-center gap-2 text-xs text-brand-600">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Uploading image…
            </div>

            @error('flyer') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>

        {{-- ── Section: Voting Rules ── --}}
        <div class="p-6 space-y-5">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Voting Rules</h2>

            {{-- Toggle: pay per vote --}}
            <label class="flex items-start gap-4 cursor-pointer">
                <div class="relative mt-0.5 shrink-0">
                    <input wire:model.live="pay_per_vote" type="checkbox" class="sr-only peer">
                    <div class="w-10 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full transition duration-200"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-200 peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Pay-per-vote</p>
                    <p class="text-xs text-slate-400">Voters pay for each vote via Mobile Money or card</p>
                </div>
            </label>

            @if($pay_per_vote)
            <div class="ml-14">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Price per Vote (pesewas)</label>
                <div class="flex items-center gap-3">
                    <input wire:model.live="price_per_vote_pesewas" type="number" min="0" step="10"
                           class="w-32 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition font-mono">
                    <div class="bg-brand-50 border border-brand-100 rounded-xl px-4 py-2 text-sm">
                        = <span class="font-bold text-brand-700">GHS {{ number_format($price_per_vote_pesewas / 100, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Max Votes per Voter</label>
                <input wire:model="max_votes_per_voter" type="number" min="1" placeholder="Unlimited"
                       class="w-32 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition font-mono">
                <p class="text-xs text-slate-400 mt-1">Leave blank for unlimited</p>
            </div>

            {{-- Toggle: eligibility list --}}
            <label class="flex items-start gap-4 cursor-pointer">
                <div class="relative mt-0.5 shrink-0">
                    <input wire:model="requires_eligibility_list" type="checkbox" class="sr-only peer">
                    <div class="w-10 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full transition duration-200"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-200 peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Eligibility List Required</p>
                    <p class="text-xs text-slate-400">Restrict voting to pre-approved members or students</p>
                </div>
            </label>

            {{-- Toggle: anonymous tally --}}
            <label class="flex items-start gap-4 cursor-pointer">
                <div class="relative mt-0.5 shrink-0">
                    <input wire:model="anonymous_tally" type="checkbox" class="sr-only peer">
                    <div class="w-10 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full transition duration-200"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all duration-200 peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Anonymous Tally</p>
                    <p class="text-xs text-slate-400">Voter phones anonymised after event closes — Act 843 compliant</p>
                </div>
            </label>
        </div>

        {{-- ── Footer: Save / Cancel ── --}}
        <div class="px-6 py-4 bg-slate-50/60 flex items-center justify-between rounded-b-2xl">
            <a href="{{ route('admin.events.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
                ← Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition shadow-sm">
                <span wire:loading.remove wire:target="save">
                    {{ $event?->exists ? 'Save Changes' : 'Create Event' }}
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Saving…
                </span>
            </button>
        </div>
    </form>

</div>
