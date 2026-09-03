<div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#241038;margin-bottom:4px">Events List</h1>
            <p style="color:#9ca3af;font-size:13.5px">Manage all your voting events.</p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#3c1f56,#4a2769);color:white;border-radius:12px;padding:11px 20px;font-size:13.5px;font-weight:700;text-decoration:none;white-space:nowrap;box-shadow:0 4px 14px rgba(45,0,80,.3)">
            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Create Event
        </a>
    </div>

    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px">
            <div style="position:relative;flex:1">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search events..."
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px 9px 36px;font-size:13.5px;outline:none;color:#241038">
            </div>
            <select wire:model.live="statusFilter"
                    style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:13.5px;color:#241038;outline:none;background:white;min-width:140px">
                <option value="">All Status</option>
                <option value="live">Live</option>
                <option value="draft">Draft</option>
                <option value="closed">Closed</option>
            </select>
            <p style="color:#9ca3af;font-size:13px;white-space:nowrap">{{ $events->total() }} events</p>
        </div>

        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Event','Type','Categories','Status','Votes','Revenue','Actions'] as $h)
                    <th style="padding:11px 20px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                @php
                    $statusC = ['live'=>['#d1fae5','#059669'],'draft'=>['#f3f4f6','#6b7280'],'closed'=>['#fee2e2','#dc2626']];
                    [$sbg,$stc] = $statusC[$event->status] ?? ['#f3f4f6','#6b7280'];
                    $revenue = \App\Models\Payment::where('event_id',$event->id)->where('status','success')->sum('amount_pesewas');
                @endphp
                <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px">
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($event->flyer_path)
                            <img src="{{ asset('storage/'.$event->flyer_path) }}" style="width:44px;height:44px;border-radius:10px;object-fit:cover;flex-shrink:0">
                            @else
                            <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#e11d7415,#6f449715);border:1px solid #f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg style="width:20px;height:20px;color:#6f4497" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <p style="font-weight:700;color:#241038;font-size:14px">{{ $event->name }}</p>
                                <p style="color:#9ca3af;font-size:12px;margin-top:1px">{{ $event->starts_at?->format('d M Y') ?? 'No date' }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 20px">
                        <span style="background:#f7f5fb;color:#6f4497;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:700;text-transform:uppercase">
                            {{ $event->event_type ?? 'voting' }}
                        </span>
                    </td>
                    <td style="padding:14px 20px;color:#6b7280;font-size:13.5px">{{ $event->categories_count }}</td>
                    <td style="padding:14px 20px">
                        <button wire:click="toggleStatus({{ $event->id }})"
                                style="background:{{ $sbg }};color:{{ $stc }};border:none;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:700;cursor:pointer;text-transform:uppercase;display:inline-flex;align-items:center;gap:5px">
                            @if($event->status==='live')<span style="width:6px;height:6px;background:#059669;border-radius:50%;display:inline-block"></span>@endif
                            {{ ucfirst($event->status) }}
                        </button>
                    </td>
                    <td style="padding:14px 20px;font-weight:700;color:#241038;font-size:15px">{{ number_format($event->votes_count) }}</td>
                    <td style="padding:14px 20px;font-weight:700;color:#059669;font-size:14px">GHS {{ number_format($revenue/100,2) }}</td>
                    <td style="padding:14px 20px">
                        <div style="display:flex;gap:10px;align-items:center">
                            <a href="{{ route('admin.events.edit', $event) }}" style="font-size:12.5px;font-weight:600;color:#6b7280;text-decoration:none">Edit</a>
                            <span style="color:#e5e7eb">·</span>
                            <a href="{{ route('admin.events.show', $event) }}" style="font-size:12.5px;font-weight:600;color:#6f4497;text-decoration:none">Manage</a>
                            <span style="color:#e5e7eb">·</span>
                            <a href="{{ route('admin.events.results', $event) }}" style="font-size:12.5px;font-weight:600;color:#e11d74;text-decoration:none">Results</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:56px;text-align:center">
                        <svg style="width:48px;height:48px;color:#e5e7eb;margin:0 auto 14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p style="color:#6b7280;font-weight:600;font-size:15px">No events yet</p>
                        <a href="{{ route('admin.events.create') }}" style="display:inline-block;margin-top:10px;color:#e11d74;font-weight:700;font-size:13.5px;text-decoration:none">Create your first event →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6">{{ $events->links() }}</div>
        @endif
    </div>
</div>
