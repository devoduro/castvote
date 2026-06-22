<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Events</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage all your voting events in one place</p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-2.5 rounded-xl transition shadow-sm text-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 flex flex-wrap gap-3 items-center">
        <div class="relative">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search events…"
                   class="pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm w-56 focus:ring-2 focus:ring-brand-500 outline-none transition">
        </div>
        <select wire:model.live="statusFilter"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition text-slate-700">
            <option value="">All statuses</option>
            <option value="draft">Draft</option>
            <option value="live">Live</option>
            <option value="closed">Closed</option>
        </select>
        <div class="ml-auto text-xs text-slate-400">
            {{ $events->total() }} event{{ $events->total() !== 1 ? 's' : '' }}
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Event</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Type</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">USSD</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Votes</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Dates</th>
                    <th class="text-right px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($events as $event)
                <tr class="hover:bg-slate-50/70 transition group">

                    {{-- Name + flyer thumb --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg overflow-hidden shrink-0 bg-slate-100 flex items-center justify-center">
                                @if($event->flyer_path)
                                    <img src="{{ asset('storage/'.$event->flyer_path) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ $event->name }}</p>
                                <p class="text-xs text-slate-400 truncate font-mono">{{ $event->slug }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Type --}}
                    <td class="px-4 py-4">
                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold
                            {{ $event->event_type === 'award' ? 'bg-purple-100 text-purple-700' : ($event->event_type === 'election' ? 'bg-blue-100 text-blue-700' : 'bg-teal-100 text-teal-700') }}">
                            {{ strtoupper($event->event_type) }}
                        </span>
                    </td>

                    {{-- USSD --}}
                    <td class="px-4 py-4 hidden md:table-cell">
                        <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                            {{ $event->ussd_shortcode ?? '—' }}
                        </span>
                    </td>

                    {{-- Status (clickable to advance) --}}
                    <td class="px-4 py-4">
                        <button wire:click="toggleStatus({{ $event->id }})"
                                title="Click to advance status"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition hover:opacity-80
                                    {{ $event->status === 'live' ? 'bg-emerald-100 text-emerald-700' : ($event->status === 'draft' ? 'bg-slate-100 text-slate-600' : 'bg-red-100 text-red-600') }}">
                            @if($event->status === 'live')
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            @endif
                            {{ strtoupper($event->status) }}
                        </button>
                    </td>

                    {{-- Votes --}}
                    <td class="px-4 py-4 text-right hidden sm:table-cell">
                        <span class="font-mono font-semibold text-slate-700">{{ number_format($event->votes_count) }}</span>
                    </td>

                    {{-- Dates --}}
                    <td class="px-4 py-4 text-right text-xs text-slate-400 hidden lg:table-cell whitespace-nowrap">
                        {{ $event->starts_at?->format('d M') ?? '—' }} → {{ $event->ends_at?->format('d M Y') ?? '—' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3 opacity-60 group-hover:opacity-100 transition">
                            <a href="{{ route('admin.events.results', $event) }}"
                               class="text-xs font-semibold text-brand-600 hover:text-brand-700">Results</a>
                            <a href="{{ route('admin.events.payments', $event) }}"
                               class="text-xs font-semibold text-blue-500 hover:text-blue-600 hidden sm:inline">Pay</a>
                            <a href="{{ route('admin.events.fraud', $event) }}"
                               class="text-xs font-semibold text-red-400 hover:text-red-500 hidden lg:inline">Fraud</a>
                            <a href="{{ route('admin.events.edit', $event) }}"
                               class="text-xs font-semibold text-slate-400 hover:text-slate-600">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-slate-600 font-semibold text-sm">No events found</p>
                        <p class="text-slate-400 text-xs mt-1">Try adjusting your filters or create a new event</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $events->links() }}
        </div>
        @endif
    </div>

</div>
