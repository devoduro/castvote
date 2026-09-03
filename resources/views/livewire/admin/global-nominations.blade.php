<div>
    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#241038;margin-bottom:4px">Nominations</h1>
            <p style="color:#9ca3af;font-size:13.5px">Manage pending applications.</p>
        </div>
        <button style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#3c1f56,#4a2769);color:white;border:none;border-radius:10px;padding:10px 18px;font-size:13.5px;font-weight:700;cursor:pointer">
            <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </button>
    </div>

    {{-- KPI cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
        @foreach([
            ['Total Applications', $counts['total'],   '#6b7280', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['Pending',           $counts['pending'],  '#d97706', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Approved',          $counts['approved'], '#059669', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Rejected',          $counts['rejected'], '#dc2626', 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as [$label, $val, $color, $path])
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:20px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $color }}1a;display:flex;align-items:center;justify-content:center">
                    <svg style="width:18px;height:18px;color:{{ $color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                    </svg>
                </div>
                <p style="font-size:12px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em">{{ $label }}</p>
            </div>
            <p style="font-size:28px;font-weight:800;color:#241038">{{ number_format($val) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <div style="position:relative;flex:1;min-width:200px">
                <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search nominees..."
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px 9px 36px;font-size:13.5px;outline:none;color:#241038">
            </div>
            <select wire:model.live="eventId"
                    style="border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:13.5px;color:#241038;outline:none;background:white;min-width:160px">
                <option value="">All Events</option>
                @foreach($events as $ev)
                <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                @endforeach
            </select>
            <div style="display:flex;gap:6px">
                @foreach(['All','Approved'] as $f)
                <button style="padding:8px 16px;border-radius:10px;font-size:13px;font-weight:600;border:none;cursor:pointer;{{ $f==='All' ? 'background:#e11d74;color:white' : 'background:#f3f4f6;color:#6b7280' }}">
                    {{ $f }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Table --}}
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#fafafa;border-bottom:1px solid #f3f4f6">
                    @foreach(['Nominee','Category / Event','Code','Votes','Actions'] as $h)
                    <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($nominees as $nominee)
                <tr style="border-bottom:1px solid #f9fafb;transition:background .1s" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px">
                        <div style="display:flex;align-items:center;gap:12px">
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#e11d74,#6f4497);display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($nominee->name,0,1)) }}
                            </div>
                            <div>
                                <p style="font-weight:600;color:#241038;font-size:14px">{{ $nominee->name }}</p>
                                @if($nominee->bio)
                                <p style="color:#9ca3af;font-size:12px;margin-top:1px">{{ Str::limit($nominee->bio, 40) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 20px">
                        <p style="font-weight:600;color:#241038;font-size:13.5px">{{ $nominee->category->name }}</p>
                        <p style="color:#9ca3af;font-size:12px">{{ $nominee->category->event->name }}</p>
                    </td>
                    <td style="padding:14px 20px">
                        <span style="background:#f7f5fb;color:#6f4497;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
                            #{{ $nominee->code }}
                        </span>
                    </td>
                    <td style="padding:14px 20px">
                        <span style="font-size:15px;font-weight:800;color:#241038">{{ number_format($nominee->votes_count) }}</span>
                    </td>
                    <td style="padding:14px 20px;text-align:right">
                        <a href="{{ route('admin.events.nominees', [$nominee->category->event, $nominee->category]) }}"
                           style="color:#e11d74;font-size:13px;font-weight:600;text-decoration:none">
                            View →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:56px 20px;text-align:center">
                        <svg style="width:40px;height:40px;color:#e5e7eb;margin:0 auto 12px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p style="color:#6b7280;font-weight:600;font-size:14px">No nominations found</p>
                        <p style="color:#9ca3af;font-size:13px;margin-top:4px">Try adjusting your filters</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($nominees->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6">
            {{ $nominees->links() }}
        </div>
        @endif
    </div>
</div>
