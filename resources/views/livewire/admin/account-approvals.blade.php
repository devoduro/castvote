<div x-data>

    <div style="margin-bottom:24px">
        <h1 style="font-size:22px;font-weight:800;color:#1a0030;margin-bottom:4px">Account Approvals</h1>
        <p style="color:#9ca3af;font-size:13.5px">Review and approve organizer account applications.</p>
    </div>

    {{-- Tab bar --}}
    <div style="display:flex;align-items:center;gap:6px;background:white;border:1px solid #e5e7eb;border-radius:14px;padding:6px;width:fit-content;margin-bottom:24px">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
        @php
            $isActive = $tab === $key;
            $badgeBg = $isActive
                ? ($key === 'pending' ? '#fef3c7' : 'rgba(255,255,255,.2)')
                : ($key === 'pending' ? '#fef3c7' : '#f3f4f6');
            $badgeTc = $isActive
                ? ($key === 'pending' ? '#92400e' : 'white')
                : ($key === 'pending' ? '#d97706' : '#6b7280');
        @endphp
        <button wire:click="$set('tab', '{{ $key }}')"
                style="display:flex;align-items:center;gap:7px;padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:all .15s;
                    {{ $isActive ? 'background:linear-gradient(135deg,#2d0050,#3b0068);color:white;box-shadow:0 4px 12px rgba(45,0,80,.25)' : 'background:transparent;color:#6b7280' }}">
            {{ $label }}
            @if($counts[$key] > 0)
            <span style="font-size:11px;font-weight:700;padding:2px 7px;border-radius:20px;background:{{ $badgeBg }};color:{{ $badgeTc }}">
                {{ $counts[$key] }}
            </span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- Accounts table --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Organizer','Organization','Contact','Status','Registered','Actions'] as $h)
                    <th style="padding:11px 20px;text-align:{{ $h==='Actions'?'right':'left' }};font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                @php
                    $sc = match($account->account_status) {
                        'pending'  => ['#fef3c7','#d97706'],
                        'approved' => ['#d1fae5','#059669'],
                        'rejected' => ['#fee2e2','#dc2626'],
                        default    => ['#f3f4f6','#6b7280'],
                    };
                @endphp
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#e91e8c,#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($account->name, 0, 2)) }}
                            </div>
                            <div>
                                <p style="font-weight:700;color:#1a0030;font-size:13.5px">{{ $account->name }}</p>
                                <p style="color:#9ca3af;font-size:12px">{{ $account->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 20px;font-size:13.5px;font-weight:500;color:#374151">
                        {{ $account->organization?->name ?? '—' }}
                    </td>
                    <td style="padding:14px 20px;font-size:12.5px;color:#6b7280">
                        {{ $account->phone ?? $account->organization?->contact_email ?? '—' }}
                    </td>
                    <td style="padding:14px 20px">
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:700;text-transform:uppercase;background:{{ $sc[0] }};color:{{ $sc[1] }}">
                            @if($account->account_status === 'pending')
                            <span style="width:6px;height:6px;background:#d97706;border-radius:50%;display:inline-block;animation:pulse 1.5s ease-in-out infinite"></span>
                            @elseif($account->account_status === 'approved')
                            <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            {{ ucfirst($account->account_status) }}
                        </span>
                    </td>
                    <td style="padding:14px 20px;font-size:12.5px;color:#9ca3af;white-space:nowrap">
                        {{ $account->created_at->format('d M Y') }}
                    </td>
                    <td style="padding:14px 20px;text-align:right">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                            @if($account->account_status !== 'approved')
                            <button
                                @click="if(confirm('Approve {{ addslashes($account->name) }}\'s account?')) $wire.approve({{ $account->id }})"
                                style="display:inline-flex;align-items:center;gap:5px;background:#059669;color:white;border:none;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;cursor:pointer">
                                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                            @endif
                            @if($account->account_status !== 'rejected')
                            <button
                                @click="if(confirm('Reject {{ addslashes($account->name) }}\'s account?')) $wire.reject({{ $account->id }})"
                                style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#dc2626;border:1.5px solid #fecaca;border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;cursor:pointer">
                                <svg style="width:11px;height:11px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:64px;text-align:center">
                        <div style="width:48px;height:48px;background:#f9fafb;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                            <svg style="width:24px;height:24px;color:#d1d5db" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <p style="font-weight:600;color:#374151;font-size:14px">No {{ $tab }} accounts</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($accounts->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $accounts->links() }}</div>
        @endif
    </div>

    <style>@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}</style>
</div>
