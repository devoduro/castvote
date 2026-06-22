<div class="max-w-md mx-auto">

    @if(!$verified)
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-br from-gray-900 to-brand-900 p-8 text-center">
            <div class="w-16 h-16 bg-white/10 border border-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-black text-white mb-1">Verify Your Eligibility</h2>
            <p class="text-gray-300 text-sm">
                This is a restricted voting event. Enter your ID to access the ballot.
            </p>
        </div>

        <div class="p-8 space-y-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    @if($event->event_type === 'election') Student Index Number
                    @elseif($event->event_type === 'agm') Shareholder / Member ID
                    @else Voter ID
                    @endif
                </label>
                <input wire:model="identifier"
                       wire:keydown.enter="verify"
                       type="text"
                       placeholder="{{ $event->event_type === 'election' ? 'e.g. 10XXXXXXX' : 'Your ID' }}"
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-base font-mono tracking-widest focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                @error('identifier')
                <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <button wire:click="verify"
                    wire:loading.attr="disabled"
                    class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-black py-3.5 rounded-2xl transition text-sm shadow-lg shadow-brand-600/25 flex items-center justify-center gap-2">
                <span wire:loading.remove class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Verify &amp; Access Ballot
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Verifying…
                </span>
            </button>

            <p class="text-center text-xs text-gray-400">
                Having trouble? Contact the event organiser for assistance.
            </p>
        </div>
    </div>

    @else
    {{-- Verified — show the ballot --}}
    <div class="bg-green-50 border-2 border-green-200 rounded-2xl px-5 py-4 mb-6 flex items-center gap-3 text-green-700">
        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="font-bold text-sm">Identity Verified</p>
            <p class="text-xs text-green-600">You may now cast your vote below.</p>
        </div>
    </div>
    <livewire:vote.ballot :event="$event" />
    @endif

</div>
