<div class="max-w-md mx-auto">

    @if(!$verified)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
        <div class="text-4xl mb-4">🔐</div>
        <h2 class="text-xl font-bold text-gray-800 mb-1">Verify Your Eligibility</h2>
        <p class="text-sm text-gray-500 mb-6">
            This is a restricted voting event. Enter your
            <strong>student index number</strong> or <strong>voter ID</strong>
            to access the ballot.
        </p>

        <div class="text-left space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    @if($event->event_type === 'election') Student Index Number
                    @elseif($event->event_type === 'agm') Shareholder / Member ID
                    @else Voter ID
                    @endif
                </label>
                <input wire:model="identifier"
                       wire:keydown.enter="verify"
                       type="text"
                       placeholder="{{ $event->event_type === 'election' ? 'e.g. 10XXXXXXX' : 'Your ID' }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm font-mono tracking-widest focus:ring-2 focus:ring-brand-500 outline-none">
                @error('identifier')
                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button wire:click="verify"
                    wire:loading.attr="disabled"
                    class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-bold py-3 rounded-xl transition text-sm">
                <span wire:loading.remove>Verify &amp; Access Ballot</span>
                <span wire:loading>Verifying…</span>
            </button>
        </div>

        <p class="text-xs text-gray-400 mt-6">
            Having trouble? Contact the event organiser for assistance.
        </p>
    </div>

    @else
    {{-- Verified — show the ballot --}}
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-6 text-sm text-green-700 text-center">
        ✅ Identity verified. You may now cast your vote.
    </div>
    <livewire:vote.ballot :event="$event" />
    @endif

</div>
