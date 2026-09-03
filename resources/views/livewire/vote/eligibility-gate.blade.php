<div>

    @if(! $verified)
        <div class="card overflow-hidden max-w-md">
            <div class="p-7 text-center" style="background:linear-gradient(140deg,#3c1f56,#14031f)">
                <span class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 text-white
                             flex items-center justify-center mx-auto mb-4">
                    <x-ui.icon name="lock" :size="30" :stroke="1.8" />
                </span>
                <h2 class="text-white font-extrabold text-[20px]">Verify your eligibility</h2>
                <p class="text-white/60 text-[13.5px] mt-2 leading-relaxed">
                    This is a restricted vote. Enter your ID to unlock the ballot.
                </p>
            </div>

            <div class="p-6 sm:p-7 flex flex-col gap-5">
                <div>
                    <label for="voter-id" class="label">
                        @if($event->event_type === 'election') Student index number
                        @elseif($event->event_type === 'agm') Shareholder or member ID
                        @else Voter ID
                        @endif
                    </label>
                    <input id="voter-id" wire:model="identifier" wire:keydown.enter="verify" type="text"
                           autocomplete="off" spellcheck="false"
                           placeholder="{{ $event->event_type === 'election' ? 'e.g. 10XXXXXXX' : 'Your ID' }}"
                           class="input font-mono tracking-widest @error('identifier') is-error @enderror"
                           @error('identifier') aria-invalid="true" aria-describedby="voter-id-err" @enderror>
                    @error('identifier')
                        <p id="voter-id-err" class="error-msg" role="alert">
                            <x-ui.icon name="warning" :size="14" /> {{ $message }}
                        </p>
                    @enderror
                </div>

                <x-ui.btn type="button" wire:click="verify" loading="verify"
                          variant="primary" size="lg" block icon="shield">
                    Verify &amp; access ballot
                </x-ui.btn>

                <p class="text-center text-[12.5px] text-ink-400">
                    Having trouble? Contact the event organiser for help.
                </p>
            </div>
        </div>
    @else
        <div class="rounded-2xl bg-green-50 border border-green-200 px-5 py-4 mb-6 flex items-center gap-3 max-w-md">
            <span class="w-9 h-9 rounded-full bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                <x-ui.icon name="check" :size="17" :stroke="3" />
            </span>
            <span>
                <span class="block text-[14px] font-bold text-green-800">Identity verified</span>
                <span class="block text-[12.5px] text-green-700">You can now cast your vote below.</span>
            </span>
        </div>

        <livewire:vote.ballot :event="$event" />
    @endif
</div>
