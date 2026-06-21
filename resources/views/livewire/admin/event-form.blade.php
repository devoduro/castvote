<div class="max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ $event?->exists ? 'Edit Event' : 'New Event' }}</h2>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Event Name</label>
            <input wire:model="name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                <select wire:model.live="event_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                    <option value="award">Award Show</option>
                    <option value="agm">Corporate AGM</option>
                    <option value="election">Student Election</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                    <option value="draft">Draft</option>
                    <option value="live">Live</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                <input wire:model="starts_at" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ends At</label>
                <input wire:model="ends_at" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">USSD Shortcode</label>
                <input wire:model="ussd_shortcode" type="text" placeholder="*928*24#"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Arkesel Service ID</label>
                <input wire:model="ussd_short_id" type="text" placeholder="240"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
        </div>

        <hr class="border-gray-100">
        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Voting Rules</h3>

        <div class="space-y-3">
            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model.live="pay_per_vote" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm text-gray-700">Pay-per-vote (voters pay for each vote)</span>
            </label>

            @if($pay_per_vote)
            <div class="ml-7">
                <label class="block text-sm font-medium text-gray-700 mb-1">Price per Vote (pesewas)</label>
                <div class="flex items-center gap-2">
                    <input wire:model="price_per_vote_pesewas" type="number" min="0" step="10"
                           class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                    <span class="text-xs text-gray-400">= GHS {{ number_format($price_per_vote_pesewas / 100, 2) }}</span>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Votes per Voter (leave blank = unlimited)</label>
                <input wire:model="max_votes_per_voter" type="number" min="1" placeholder="Unlimited"
                       class="w-32 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="requires_eligibility_list" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm text-gray-700">Requires eligibility list (AGM / elections)</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <input wire:model="anonymous_tally" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm text-gray-700">Anonymous tally (voter phone anonymised after event closes)</span>
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button wire:click="save" wire:loading.attr="disabled"
                    class="bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white font-semibold px-6 py-2 rounded-lg text-sm transition">
                <span wire:loading.remove>Save Event</span>
                <span wire:loading>Saving…</span>
            </button>
            <a href="{{ route('admin.events.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-4 py-2">Cancel</a>
        </div>
    </div>
</div>
