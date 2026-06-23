<div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#1a0030;margin-bottom:4px">Audit Log</h1>
            <p style="color:#9ca3af;font-size:13.5px">Complete history of all admin actions in your account.</p>
        </div>
        <div style="position:relative">
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Filter by action..."
                   style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px 9px 36px;font-size:13.5px;outline:none;color:#1a0030;width:240px">
        </div>
    </div>

    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Action','Subject','Admin','IP Address','Date & Time'] as $h)
                    <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $actionColors = [
                        'account'      => ['#dbeafe','#1d4ed8'],
                        'event'        => ['#f3e8ff','#7c3aed'],
                        'payment'      => ['#d1fae5','#059669'],
                        'organizer'    => ['#fef3c7','#d97706'],
                        'vote'         => ['#fce7f3','#be185d'],
                        'password'     => ['#fee2e2','#dc2626'],
                        'profile'      => ['#e0f2fe','#0369a1'],
                    ];
                    $prefix = explode('.', $log->action)[0];
                    [$bg, $tc] = $actionColors[$prefix] ?? ['#f3f4f6','#6b7280'];
                @endphp
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:13px 20px">
                        <span style="background:{{ $bg }};color:{{ $tc }};padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td style="padding:13px 20px">
                        @if($log->subject_type && $log->subject_id)
                        <p style="font-size:13px;color:#6b7280">
                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                        </p>
                        @else
                        <p style="color:#d1d5db">—</p>
                        @endif
                        @if($log->meta && count($log->meta))
                        <p style="font-size:11px;color:#9ca3af;font-family:monospace;margin-top:2px">
                            {{ Str::limit(json_encode($log->meta), 50) }}
                        </p>
                        @endif
                    </td>
                    <td style="padding:13px 20px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#e91e8c,#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($log->admin?->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:600;color:#1a0030">{{ $log->admin?->name ?? 'System' }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:13px 20px;font-family:monospace;font-size:12.5px;color:#6b7280">{{ $log->ip_address ?? '—' }}</td>
                    <td style="padding:13px 20px;font-size:12.5px;color:#9ca3af;white-space:nowrap">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:56px;text-align:center">
                        <svg style="width:40px;height:40px;color:#e5e7eb;margin:0 auto 12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p style="color:#6b7280;font-weight:600;font-size:14px">No audit records found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
