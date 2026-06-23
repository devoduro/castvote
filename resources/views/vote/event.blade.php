<x-layouts.public>
<x-slot name="title">Vote — {{ $event->name }}</x-slot>

{{-- Event Hero --}}
<div style="position:relative;background:#1a0030;overflow:hidden">
    @if($event->flyerUrl())
    <img src="{{ $event->flyerUrl() }}" alt="{{ $event->name }}"
         style="width:100%;height:320px;object-fit:cover;display:block;opacity:.55">
    @else
    <div style="height:220px;background:linear-gradient(135deg,#2d0050,#1a0030)">
        <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(233,30,140,.15) 1px,transparent 1px);background-size:22px 22px"></div>
    </div>
    @endif
    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(26,0,48,.92) 0%,rgba(26,0,48,.4) 60%,transparent 100%)"></div>
    <div style="position:absolute;bottom:0;left:0;right:0;padding:28px;max-width:1160px;margin:0 auto">
        <a href="{{ route('vote.index') }}" style="display:inline-flex;align-items:center;gap:5px;color:rgba(255,255,255,.55);font-size:13px;margin-bottom:12px;text-decoration:none;transition:color .15s" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,.55)'">
            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            All Events
        </a>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
            <span style="background:#e91e8c;color:white;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase">
                {{ $event->event_type === 'award' ? 'Voting' : strtoupper($event->event_type) }}
            </span>
            <span style="display:flex;align-items:center;gap:4px;background:rgba(5,150,105,.2);border:1px solid rgba(5,150,105,.4);color:#34d399;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px">
                <span style="width:5px;height:5px;background:#4ade80;border-radius:50%;display:inline-block;animation:pulse 1.5s ease-in-out infinite"></span>
                LIVE
            </span>
        </div>
        <h1 style="font-size:32px;font-weight:900;color:white;margin:0 0 10px;line-height:1.2;letter-spacing:-.3px">{{ $event->name }}</h1>
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:14px">
            <span style="display:flex;align-items:center;gap:5px;color:rgba(255,255,255,.6);font-size:13.5px">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Closes {{ $event->ends_at?->format('d M Y, g:ia') ?? 'TBD' }}
            </span>
            @if($event->isPayPerVote())
            <span style="color:#f9a8d4;font-weight:600;font-size:13.5px">GHS {{ $event->priceInGhs() }} per vote</span>
            @else
            <span style="color:#4ade80;font-weight:600;font-size:13.5px">✓ Free to vote</span>
            @endif
        </div>
    </div>
</div>

{{-- Voting options strip --}}
<div style="background:linear-gradient(90deg,#2d0050,#1a0030);padding:14px 28px">
    <div style="max-width:1160px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:16px">
            <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.7);font-size:13px">
                <svg style="width:16px;height:16px;color:#e91e8c" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>Vote via USSD:</span>
                <span style="font-family:monospace;font-weight:800;color:white;font-size:15px;letter-spacing:.05em">
                    {{ $event->ussd_shortcode ?? '*928#' }}
                </span>
                <span style="color:rgba(255,255,255,.4);font-size:12px">on any Ghana network</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.5)">
            <svg style="width:14px;height:14px;color:#4ade80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Secured by Paystack &bull; MTN · Telecel · AirtelTigo
        </div>
    </div>
</div>

{{-- Ballot --}}
<div style="max-width:1160px;margin:0 auto;padding:36px 24px 80px">

    {{-- Two-column layout: Online ballot + USSD guide --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start">

        {{-- Online ballot --}}
        <div>
            <div style="margin-bottom:22px">
                <h2 style="font-size:20px;font-weight:800;color:#1a0030;margin-bottom:4px">Vote Online</h2>
                <p style="color:#9ca3af;font-size:13.5px">Choose a category below to view nominees and cast your vote.</p>
            </div>

            @if($event->requiresEligibilityList())
                <livewire:vote.eligibility-gate :event="$event" />
            @else
                <livewire:vote.ballot :event="$event" />
            @endif
        </div>

        {{-- USSD Guide sidebar --}}
        <div>
            <div style="background:linear-gradient(135deg,#2d0050,#1a0030);border-radius:20px;padding:24px;color:white;position:sticky;top:80px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
                    <div style="width:36px;height:36px;background:rgba(233,30,140,.25);border-radius:10px;display:flex;align-items:center;justify-content:center">
                        <svg style="width:18px;height:18px;color:#e91e8c" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13.5px;font-weight:800">Vote via USSD</p>
                        <p style="font-size:11.5px;color:rgba(255,255,255,.5)">No internet needed</p>
                    </div>
                </div>

                <div style="background:rgba(255,255,255,.06);border-radius:12px;padding:14px;margin-bottom:16px;text-align:center">
                    <p style="font-size:11px;color:rgba(255,255,255,.5);margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em">Dial this code</p>
                    <p style="font-family:monospace;font-size:28px;font-weight:900;color:#e91e8c;letter-spacing:.05em">{{ $event->ussd_shortcode ?? '*928#' }}</p>
                    <p style="font-size:11.5px;color:rgba(255,255,255,.4);margin-top:4px">Works on any Ghana network</p>
                </div>

                <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:16px">
                    @php
                        $ussdSteps = [
                            ['1', 'Dial the shortcode above from your mobile phone.'],
                            ['2', 'Select the event category you want to vote for.'],
                            ['3', 'Enter the nominee code to cast your vote.'],
                        ];
                        if ($event->isPayPerVote()) {
                            $ussdSteps[] = ['4', 'Pay GHS '.$event->priceInGhs().' per vote via Mobile Money prompt.'];
                        }
                    @endphp
                    @foreach($ussdSteps as [$step, $desc])
                    <div style="display:flex;align-items:flex-start;gap:10px">
                        <div style="width:22px;height:22px;border-radius:50%;background:rgba(233,30,140,.25);color:#e91e8c;font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">{{ $step }}</div>
                        <p style="font-size:13px;color:rgba(255,255,255,.7);line-height:1.5">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>

                <div style="border-top:1px solid rgba(255,255,255,.1);padding-top:14px">
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:rgba(255,255,255,.4)">
                        <svg style="width:13px;height:13px;color:#4ade80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Secured by Paystack. Votes are final and cannot be reversed.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.public>
