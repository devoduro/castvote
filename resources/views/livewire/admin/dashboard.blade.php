<div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Live Events</p>
            <p class="text-3xl font-bold text-orange-600 mt-1">{{ $liveEvents }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Votes</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalVotes) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Revenue (GHS)</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($totalRevenue / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Pending Payments</p>
            <p class="text-3xl font-bold {{ $pendingCount > 0 ? 'text-amber-500' : 'text-gray-400' }} mt-1">{{ $pendingCount }}</p>
        </div>
    </div>

    {{-- Events table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700">Your Events</h3>
            <a href="{{ route('admin.events.create') }}"
               class="bg-orange-600 hover:bg-orange-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition">
                + New Event
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-6 py-3">Event</th>
                    <th class="text-left px-6 py-3">Type</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-right px-6 py-3">Votes</th>
                    <th class="text-right px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($events as $event)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $event->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : ($event->event_type === 'election' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                            {{ strtoupper($event->event_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $event->status === 'live' ? 'bg-green-100 text-green-700' : ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-600') }}">
                            {{ strtoupper($event->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-mono">{{ number_format($event->votes_count) }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.events.results', $event) }}" class="text-orange-600 hover:underline text-xs">Results</a>
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No events yet. Create your first event.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
