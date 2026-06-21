<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Events</h2>
        <a href="{{ route('admin.events.create') }}"
           class="bg-orange-600 hover:bg-orange-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition">
            + New Event
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search events…"
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:ring-2 focus:ring-orange-500 outline-none">
        <select wire:model.live="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="live">Live</option>
            <option value="closed">Closed</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-6 py-3">Event</th>
                    <th class="text-left px-6 py-3">Type</th>
                    <th class="text-left px-6 py-3">USSD</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-right px-6 py-3">Votes</th>
                    <th class="text-right px-6 py-3">Dates</th>
                    <th class="text-right px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $event->name }}</div>
                        <div class="text-xs text-gray-400">{{ $event->slug }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : ($event->event_type === 'election' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                            {{ strtoupper($event->event_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $event->ussd_shortcode ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <button wire:click="toggleStatus({{ $event->id }})"
                                class="px-2 py-0.5 rounded-full text-xs font-medium cursor-pointer
                                    {{ $event->status === 'live' ? 'bg-green-100 text-green-700' : ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-600') }}"
                                title="Click to advance status">
                            {{ strtoupper($event->status) }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-right font-mono">{{ number_format($event->votes_count) }}</td>
                    <td class="px-6 py-4 text-right text-xs text-gray-400">
                        {{ $event->starts_at?->format('d M') ?? '—' }} →
                        {{ $event->ends_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.events.results', $event) }}" class="text-orange-600 hover:underline text-xs font-medium">Results</a>
                        <a href="{{ route('admin.events.payments', $event) }}" class="text-blue-600 hover:underline text-xs">Payments</a>
                        <a href="{{ route('admin.events.fraud', $event) }}" class="text-red-500 hover:underline text-xs">Fraud</a>
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-3 border-t border-gray-100">{{ $events->links() }}</div>
    </div>
</div>
