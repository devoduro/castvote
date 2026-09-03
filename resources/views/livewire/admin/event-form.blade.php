<div class="max-w-3xl">

    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold text-ink-900">
            {{ $event?->exists ? 'Edit event' : 'Create a new event' }}
        </h1>
        <p class="text-[13.5px] text-ink-400 mt-1">
            {{ $event?->exists
                ? 'Update the settings for this campaign.'
                : 'Fill in the details to launch a new voting campaign.' }}
        </p>
    </div>

    @if($errors->any())
        <div class="rounded-2xl bg-red-50 border border-red-200 p-4 mb-5" role="alert">
            <p class="flex items-center gap-2 text-[13.5px] font-bold text-red-700 mb-1.5">
                <x-ui.icon name="warning" :size="16" /> Please fix the following:
            </p>
            <ul class="list-disc pl-6 text-[13px] text-red-700 flex flex-col gap-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save" class="flex flex-col gap-4">

        {{-- ── Event details ── --}}
        <fieldset class="card p-5 sm:p-6">
            <legend class="text-[11px] font-extrabold uppercase tracking-[.1em] text-ink-400 mb-5">Event details</legend>

            <div class="mb-4">
                <label for="ev-name" class="label">Event name <span class="text-brand-600">*</span></label>
                <input id="ev-name" wire:model="name" type="text" placeholder="e.g. Ghana Music Awards 2025"
                       class="input @error('name') is-error @enderror" required
                       @error('name') aria-invalid="true" @enderror>
                @error('name')<p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="ev-desc" class="label">Short description</label>
                <textarea id="ev-desc" wire:model="description" rows="3" maxlength="600"
                          placeholder="One or two sentences shown on the public award page."
                          class="input @error('description') is-error @enderror"></textarea>
                @error('description')
                    <p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                @else
                    <p class="hint">Appears under the award title on the public site. Up to 600 characters.</p>
                @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="ev-type" class="label">Event type <span class="text-brand-600">*</span></label>
                    <select id="ev-type" wire:model.live="event_type" class="input">
                        <option value="award">Award show</option>
                        <option value="agm">Corporate AGM</option>
                        <option value="election">Student election</option>
                    </select>
                </div>
                <div>
                    <label for="ev-status" class="label">Status</label>
                    <select id="ev-status" wire:model="status" class="input">
                        <option value="draft">Draft — hidden from the public site</option>
                        <option value="live">Live — open for voting</option>
                        <option value="closed">Closed — voting ended</option>
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="ev-start" class="label">Voting starts</label>
                    <input id="ev-start" wire:model="starts_at" type="datetime-local" class="input">
                </div>
                <div>
                    <label for="ev-end" class="label">Voting ends</label>
                    <input id="ev-end" wire:model="ends_at" type="datetime-local"
                           class="input @error('ends_at') is-error @enderror">
                    @error('ends_at')<p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="ev-ussd" class="label">USSD shortcode</label>
                    <input id="ev-ussd" wire:model="ussd_shortcode" type="text" placeholder="*928*24#"
                           class="input font-mono">
                    <p class="hint">Shown to voters who have no internet.</p>
                </div>
                <div>
                    <label for="ev-sid" class="label">Arkesel service ID</label>
                    <input id="ev-sid" wire:model="ussd_short_id" type="text" placeholder="240" class="input font-mono">
                </div>
            </div>
        </fieldset>

        {{-- ── Flyer ── --}}
        <fieldset class="card p-5 sm:p-6">
            <legend class="text-[11px] font-extrabold uppercase tracking-[.1em] text-ink-400">Event flyer</legend>
            <p class="text-[12.5px] text-ink-400 mb-4 mt-1.5">
                Used as the banner on the public award page. Recommended 1200×630px, PNG or JPG, max 2&nbsp;MB.
            </p>

            @if($flyer)
                <div class="flex items-start gap-4 mb-4">
                    <img src="{{ $flyer->temporaryUrl() }}" alt="Preview of the new flyer"
                         class="h-[110px] w-auto rounded-xl object-cover border-2 border-brand-500">
                    <div>
                        <p class="text-[13px] font-bold text-brand-700">New flyer selected</p>
                        <p class="text-[12.5px] text-ink-400 mt-0.5">It will be saved when you submit the form.</p>
                    </div>
                </div>
            @elseif($existingFlyerPath)
                <div class="flex items-start gap-4 mb-4">
                    <img src="{{ asset('storage/' . $existingFlyerPath) }}" alt="Current event flyer"
                         class="h-[110px] w-auto rounded-xl object-cover border border-ink-100">
                    <div>
                        <p class="text-[13px] font-bold text-ink-900">Current flyer</p>
                        <p class="text-[12.5px] text-ink-400 mt-0.5">Upload a new image below to replace it.</p>
                    </div>
                </div>
            @endif

            <label class="flex flex-col items-center justify-center w-full rounded-2xl border-2 border-dashed
                          border-ink-200 p-8 cursor-pointer transition hover:border-brand-400 hover:bg-brand-50/50">
                <x-ui.icon name="download" :size="30" :stroke="1.5" class="text-ink-400 mb-2.5 rotate-180" />
                <span class="text-[13.5px] font-bold text-ink-700">
                    {{ $existingFlyerPath && ! $flyer ? 'Click to replace the flyer' : 'Click to upload an event flyer' }}
                </span>
                <span class="text-[12px] text-ink-400 mt-1">PNG, JPG or WEBP — max 2 MB</span>
                <input wire:model="flyer" type="file" accept="image/*" class="sr-only">
            </label>

            <p wire:loading wire:target="flyer" class="flex items-center gap-2 text-[12.5px] font-semibold text-brand-700 mt-2.5">
                <span class="btn-spin" style="border-color:#fecce0;border-top-color:#e11d74"></span> Uploading image…
            </p>
            @error('flyer')<p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>@enderror
        </fieldset>

        {{-- ── Voting rules ── --}}
        <fieldset class="card p-5 sm:p-6">
            <legend class="text-[11px] font-extrabold uppercase tracking-[.1em] text-ink-400 mb-5">Voting rules</legend>

            <div class="flex flex-col gap-5">
                <div>
                    <x-ui.toggle model="pay_per_vote" live :checked="$pay_per_vote"
                                 label="Pay per vote"
                                 hint="Voters pay for each vote by Mobile Money or card." />

                    @if($pay_per_vote)
                        <div class="ml-[58px] mt-4">
                            <label for="ev-price" class="label">Price per vote (pesewas)</label>
                            <div class="flex flex-wrap items-center gap-3">
                                <input id="ev-price" wire:model.live="price_per_vote_pesewas" type="number" min="0" step="10"
                                       class="input font-mono" style="width:120px">
                                <span class="rounded-xl bg-brand-50 border border-brand-100 px-3.5 py-2 text-[13.5px] text-ink-600">
                                    = <strong class="text-brand-700">GH&#8373;{{ number_format($price_per_vote_pesewas / 100, 2) }}</strong> per vote
                                </span>
                            </div>
                            @error('price_per_vote_pesewas')
                                <p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div>
                    <label for="ev-max" class="label">Maximum votes per voter</label>
                    <input id="ev-max" wire:model="max_votes_per_voter" type="number" min="1" placeholder="Unlimited"
                           class="input font-mono" style="width:140px">
                    <p class="hint">Leave blank for unlimited.</p>
                </div>

                <hr class="border-ink-100">

                <x-ui.toggle model="requires_eligibility_list" :checked="$requires_eligibility_list"
                             label="Eligibility list required"
                             hint="Restrict voting to pre-approved members or students." />

                <x-ui.toggle model="anonymous_tally" :checked="$anonymous_tally"
                             label="Anonymous tally"
                             hint="Voter phone numbers are anonymised after the event closes — Act 843 compliant." />

                <x-ui.toggle model="public_results" :checked="$public_results"
                             label="Publish results publicly"
                             hint="Show live standings for this campaign on the public results page. Off means vote counts stay visible to your team only." />

                @if($public_results && $anonymous_tally)
                    <p class="flex items-start gap-2.5 rounded-xl bg-amber-50 border border-amber-200 p-3.5 text-[12.5px] text-amber-900 leading-relaxed">
                        <x-ui.icon name="info" :size="16" class="mt-px" />
                        An anonymous tally never exposes per-nominee counts, so this campaign stays off the public
                        results page while that setting is on.
                    </p>
                @endif
            </div>
        </fieldset>

        {{-- ── Footer ── --}}
        <div class="flex items-center justify-between gap-4 pt-1">
            <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">
                <x-ui.icon name="arrow-left" :size="15" /> Cancel
            </a>
            <x-ui.btn type="submit" loading="save" variant="primary" size="lg">
                {{ $event?->exists ? 'Save changes' : 'Create event' }}
            </x-ui.btn>
        </div>
    </form>
</div>
