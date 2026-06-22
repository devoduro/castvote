<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">Account Approvals</h1>
        <p class="text-sm text-slate-500 mt-0.5">Review and approve organizer account applications</p>
    </div>

    {{-- Tab bar --}}
    <div class="flex items-center gap-1 bg-white rounded-2xl border border-slate-100 shadow-sm p-1.5 w-fit">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
        <button wire:click="$set('tab', '{{ $key }}')"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition
                    {{ $tab === $key
                        ? 'bg-navy-900 text-white shadow-sm'
                        : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}"
                style="{{ $tab === $key ? 'background:linear-gradient(135deg,#0d1526,#1e293b)' : '' }}">
            {{ $label }}
            @if($counts[$key] > 0)
            <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full
                {{ $tab === $key
                    ? ($key === 'pending' ? 'bg-amber-400 text-amber-900' : 'bg-white/20 text-white')
                    : ($key === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500') }}">
                {{ $counts[$key] }}
            </span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- Accounts table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Organizer</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Organization</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Contact</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Registered</th>
                    <th class="text-right px-6 py-3.5 text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($accounts as $account)
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold shrink-0"
                                 style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e)">
                                {{ strtoupper(substr($account->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $account->name }}</p>
                                <p class="text-xs text-slate-400">{{ $account->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 hidden md:table-cell">
                        <p class="font-medium text-slate-700">{{ $account->organization?->name ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-4 hidden lg:table-cell text-xs text-slate-500">
                        {{ $account->phone ?? $account->organization?->contact_email ?? '—' }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $account->account_status === 'pending'  ? 'bg-amber-100 text-amber-700' :
                               ($account->account_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600') }}">
                            @if($account->account_status === 'pending')
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                            @elseif($account->account_status === 'approved')
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                            {{ ucfirst($account->account_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 hidden sm:table-cell text-xs text-slate-400">
                        {{ $account->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($account->account_status !== 'approved')
                            <button wire:click="approve({{ $account->id }})"
                                    wire:confirm="Approve {{ $account->name }}'s account?"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve
                            </button>
                            @endif
                            @if($account->account_status !== 'rejected')
                            <button wire:click="reject({{ $account->id }})"
                                    wire:confirm="Reject {{ $account->name }}'s account?"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 transition">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <p class="text-slate-600 font-semibold text-sm">No {{ $tab }} accounts</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($accounts->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $accounts->links() }}
        </div>
        @endif
    </div>
</div>
