<x-layouts.public>
<x-slot name="title">Voting Events — CastVote Ghana</x-slot>

{{-- Organizer hero banner --}}
<div style="background:linear-gradient(135deg,#2d0050 0%,#1a0030 100%);padding:48px 24px;position:relative;overflow:hidden">
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(233,30,140,.12) 1px,transparent 1px);background-size:28px 28px"></div>
    <div style="max-width:1160px;margin:0 auto;display:grid;grid-template-columns:1fr auto;gap:32px;align-items:center;position:relative">
        <div>
            <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(233,30,140,.2);border:1px solid rgba(233,30,140,.35);color:#f9a8d4;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:14px;letter-spacing:.05em">
                🚀 ORGANIZER PORTAL
            </div>
            <h2 style="font-size:32px;font-weight:900;color:white;margin:0 0 10px;letter-spacing:-.3px;line-height:1.2">
                Host World-Class<br><span style="color:#e91e8c">Voting Events</span> with CastVote
            </h2>
            <p style="color:rgba(255,255,255,.6);font-size:15px;margin:0 0 22px;max-width:480px;line-height:1.6">
                Award shows, corporate AGMs, student elections — set up in minutes, collect payments via Mobile Money, track results live.
            </p>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                <a href="{{ route('admin.register') }}"
                   style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#e91e8c,#c2185b);color:white;font-size:14.5px;font-weight:700;padding:13px 28px;border-radius:12px;text-decoration:none;box-shadow:0 6px 20px rgba(233,30,140,.4)">
                    Create Free Account →
                </a>
                <a href="{{ route('admin.login') }}"
                   style="display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.25);color:white;font-size:14px;font-weight:600;padding:12px 24px;border-radius:12px;text-decoration:none">
                    Sign In
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;min-width:280px">
            @foreach([
                [$totalVotes . '+', 'Votes Cast', '#e91e8c'],
                [$totalEvents . '+', 'Events Hosted', '#c084fc'],
                [$liveCount, 'Live Now', '#4ade80'],
                ['100%', 'Secure', '#60a5fa'],
            ] as [$v, $l, $c])
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:16px;text-align:center">
                <p style="font-size:24px;font-weight:900;color:{{ $c }};margin-bottom:4px">{{ $v }}</p>
                <p style="font-size:12px;color:rgba(255,255,255,.45)">{{ $l }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div style="max-width:1160px;margin:0 auto;padding:48px 20px 80px" x-data="{ filter: 'all', search: '' }">

    {{-- Header --}}
    <div style="margin-bottom:36px">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px">
            <span style="width:8px;height:8px;background:#e91e8c;border-radius:50%;display:inline-block;animation:pulse 1.5s ease-in-out infinite"></span>
            <span style="font-size:12.5px;font-weight:700;color:#e91e8c;text-transform:uppercase;letter-spacing:.1em">Events Happening Now</span>
        </div>
        <div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:20px">
            <h1 style="font-size:42px;font-weight:900;color:#1a0030;margin:0;letter-spacing:-.5px;line-height:1.1">Voting Events</h1>

            {{-- Search + filters --}}
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                {{-- Search --}}
                <div style="position:relative">
                    <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input x-model="search" type="search" placeholder="Search events..."
                           style="border:1.5px solid #e5e7eb;border-radius:24px;padding:9px 16px 9px 36px;font-size:14px;outline:none;background:white;color:#1a0030;width:200px"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                {{-- Filter pills --}}
                @foreach(['all' => 'All', 'live' => 'Live', 'closed' => 'Ended'] as $key => $label)
                <button @click="filter = '{{ $key }}'"
                        :style="filter === '{{ $key }}'
                            ? 'background:#1a0030;color:white;border:1.5px solid #1a0030'
                            : 'background:white;color:#374151;border:1.5px solid #e5e7eb'"
                        style="padding:8px 20px;border-radius:24px;font-size:14px;font-weight:600;cursor:pointer;transition:all .15s">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Event grid --}}
    @if($events->isEmpty())
    <div style="text-align:center;padding:80px 20px">
        <div style="width:72px;height:72px;background:#f3f0ff;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg style="width:36px;height:36px;color:#7c3aed" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 style="font-size:22px;font-weight:700;color:#1a0030;margin-bottom:8px">No live events right now</h2>
        <p style="color:#9ca3af;font-size:15px">Check back soon or contact your event organiser.</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:22px">
        @foreach($events as $event)
        @php $isLive = $event->status === 'live'; @endphp
        <div
            x-show="
                (filter === 'all' || (filter === 'live' && {{ $isLive ? 'true' : 'false' }}) || (filter === 'closed' && {{ !$isLive ? 'true' : 'false' }}))
                && (search === '' || '{{ strtolower($event->name) }}'.includes(search.toLowerCase()))
            "
            style="background:white;border-radius:18px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.07);transition:box-shadow .2s;display:flex;flex-direction:column"
            onmouseover="this.style.boxShadow='0 8px 28px rgba(45,0,80,.14)'" onmouseout="this.style.boxShadow='0 1px 4px rgba(0,0,0,.07)'">

            {{-- Flyer / image --}}
            <div style="position:relative;overflow:hidden;aspect-ratio:16/9;background:#f3f0ff">
                @if($event->flyer_path)
                <img src="{{ asset('storage/'.$event->flyer_path) }}" alt="{{ $event->name }}"
                     style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s"
                     onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                @else
                <div style="width:100%;height:100%;background:linear-gradient(135deg,#e91e8c20,#7c3aed30);display:flex;align-items:center;justify-content:center">
                    <svg style="width:48px;height:48px;color:#7c3aed;opacity:.4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif

                {{-- LIVE badge --}}
                @if($isLive)
                <div style="position:absolute;top:10px;left:10px;display:flex;align-items:center;gap:5px;background:#059669;color:white;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700">
                    <span style="width:5px;height:5px;background:white;border-radius:50%;animation:pulse 1.5s ease-in-out infinite"></span>
                    LIVE
                </div>
                @else
                <div style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,.55);color:white;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:700">
                    ENDED
                </div>
                @endif

                {{-- Price badge --}}
                @if($event->isPayPerVote())
                <div style="position:absolute;top:10px;right:10px;background:rgba(255,255,255,.92);backdrop-filter:blur(4px);color:#1a0030;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700">
                    GHS {{ $event->priceInGhs() }}/vote
                </div>
                @endif
            </div>

            {{-- Card body --}}
            <div style="padding:18px 18px 20px;flex:1;display:flex;flex-direction:column;gap:10px">
                {{-- Type tag --}}
                <div style="display:flex;align-items:center;gap:6px">
                    <svg style="width:13px;height:13px;color:#e91e8c" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="font-size:11px;font-weight:700;color:#e91e8c;text-transform:uppercase;letter-spacing:.07em">
                        {{ $event->event_type === 'award' ? 'Voting' : strtoupper($event->event_type) }}
                    </span>
                </div>

                {{-- Name --}}
                <h3 style="font-size:16px;font-weight:800;color:#1a0030;margin:0;line-height:1.3">{{ $event->name }}</h3>

                {{-- Meta --}}
                <div style="display:flex;flex-direction:column;gap:5px">
                    @if($event->organization?->name)
                    <div style="display:flex;align-items:center;gap:6px">
                        <svg style="width:13px;height:13px;color:#9ca3af;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span style="font-size:13px;color:#6b7280">{{ $event->organization->name }}</span>
                    </div>
                    @endif
                    @if($event->ends_at)
                    <div style="display:flex;align-items:center;gap:6px">
                        <svg style="width:13px;height:13px;color:#9ca3af;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size:13px;color:#6b7280">{{ $event->ends_at->format('D, d M Y') }}</span>
                    </div>
                    @endif
                </div>

                {{-- CTA --}}
                <div style="margin-top:auto;padding-top:10px">
                    @if($isLive)
                    <a href="{{ route('vote.event', $event->slug) }}"
                       style="display:block;text-align:center;background:#1a0030;color:white;font-size:14px;font-weight:700;padding:11px;border-radius:12px;text-decoration:none;transition:background .15s"
                       onmouseover="this.style.background='#2d0050'" onmouseout="this.style.background='#1a0030'">
                        Vote Now
                    </a>
                    @else
                    <div style="display:block;text-align:center;background:#f3f4f6;color:#9ca3af;font-size:14px;font-weight:600;padding:11px;border-radius:12px;cursor:default">
                        Voting Closed
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- How it works ── --}}
    <div id="how-it-works" style="margin-top:80px">
        <div style="text-align:center;margin-bottom:48px">
            <p style="font-size:12px;font-weight:700;color:#e91e8c;text-transform:uppercase;letter-spacing:.12em;margin-bottom:10px">Simple &amp; Fast</p>
            <h2 style="font-size:36px;font-weight:900;color:#1a0030;margin:0 0 12px;letter-spacing:-.3px">How It Works</h2>
            <p style="color:#6b7280;font-size:15.5px;max-width:440px;margin:0 auto;line-height:1.6">Launch your event and start earning in three simple steps.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
            @foreach([
                ['01','Create','Set up your event in minutes. Customise categories, nominees, and voting rules from your dashboard.','M12 6v6m0 0v6m0-6h6m-6 0H6','#e91e8c'],
                ['02','Share','Share your unique voting link via WhatsApp and social media to engage your audience instantly.','M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z','#7c3aed'],
                ['03','Get Paid','Track revenue in real-time. Receive payouts directly to your Mobile Money wallet or Bank Account.','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','#059669'],
            ] as [$num, $title, $desc, $path, $color])
            <div style="background:white;border-radius:20px;padding:32px 28px;text-align:center;border:1px solid #e5e7eb">
                <div style="width:56px;height:56px;border-radius:16px;background:{{ $color }}14;display:flex;align-items:center;justify-content:center;margin:0 auto 18px">
                    <svg style="width:26px;height:26px;color:{{ $color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/>
                    </svg>
                </div>
                <p style="font-size:36px;font-weight:900;color:{{ $color }};margin:0 0 6px;letter-spacing:-1px">{{ $num }}</p>
                <h3 style="font-size:18px;font-weight:800;color:#1a0030;margin:0 0 10px">{{ $title }}</h3>
                <p style="font-size:13.5px;color:#6b7280;line-height:1.65;margin:0">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Stats strip --}}
    <div style="margin-top:60px;background:linear-gradient(135deg,#2d0050,#1a0030);border-radius:20px;padding:40px;display:grid;grid-template-columns:repeat(4,1fr);gap:20px">
        @foreach([
            [$totalVotes . '+', 'Votes Processed', '#e91e8c'],
            [$totalEvents . '+', 'Events Hosted',   '#c084fc'],
            [$liveCount,         'Live Now',         '#4ade80'],
            ['100%',             'Secure & Compliant', '#60a5fa'],
        ] as [$val, $label, $color])
        <div style="text-align:center">
            <p style="font-size:36px;font-weight:900;color:{{ $color }};margin:0 0 6px">{{ $val }}</p>
            <p style="font-size:13px;color:rgba(255,255,255,.5);margin:0">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- CTA banner --}}
    <div style="margin-top:60px;text-align:center">
        <h2 style="font-size:30px;font-weight:900;color:#1a0030;margin:0 0 10px">Don't Miss the Next Big Event</h2>
        <p style="color:#6b7280;font-size:15px;margin:0 0 28px">Hosting an award show or election? Get started in minutes.</p>
        <a href="{{ route('admin.register') }}"
           style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#e91e8c,#c2185b);color:white;font-size:15px;font-weight:700;padding:14px 32px;border-radius:14px;text-decoration:none;box-shadow:0 6px 20px rgba(233,30,140,.35)">
            Host an Event →
        </a>
    </div>

</div>

</x-layouts.public>
