<div style="max-width:680px">

    <div style="margin-bottom:28px">
        <h1 style="font-size:22px;font-weight:800;color:#1a0030;margin-bottom:4px">
            {{ $event?->exists ? 'Edit Event' : 'Create New Event' }}
        </h1>
        <p style="color:#9ca3af;font-size:13.5px">
            {{ $event?->exists ? 'Update the event settings below.' : 'Fill in the details to launch a new voting event.' }}
        </p>
    </div>

    @if($errors->any())
    <div style="background:#fff5f5;border:1.5px solid #fecaca;border-radius:12px;padding:14px 18px;margin-bottom:20px">
        <p style="font-size:13px;font-weight:700;color:#dc2626;margin-bottom:6px">Please fix the following errors:</p>
        <ul style="color:#dc2626;font-size:12.5px;padding-left:16px;margin:0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form wire:submit="save">

        {{-- Event Details --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px;margin-bottom:16px">
            <p style="font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:18px">Event Details</p>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">
                    Event Name <span style="color:#e91e8c">*</span>
                </label>
                <input wire:model="name" type="text" placeholder="e.g. Ghana Music Awards 2025"
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                       onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                @error('name')<p style="color:#ef4444;font-size:12px;margin-top:3px">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Event Type <span style="color:#e91e8c">*</span></label>
                    <select wire:model.live="event_type"
                            style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                            onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                        <option value="award">🏆 Award Show</option>
                        <option value="agm">🏢 Corporate AGM</option>
                        <option value="election">🗳️ Student Election</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Status</label>
                    <select wire:model="status"
                            style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                            onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                        <option value="draft">Draft</option>
                        <option value="live">Live</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Starts At</label>
                    <input wire:model="starts_at" type="datetime-local"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Ends At</label>
                    <input wire:model="ends_at" type="datetime-local"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">USSD Shortcode</label>
                    <input wire:model="ussd_shortcode" type="text" placeholder="*928*24#"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;font-family:monospace;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Arkesel Service ID</label>
                    <input wire:model="ussd_short_id" type="text" placeholder="240"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px;font-size:13.5px;font-family:monospace;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
            </div>
        </div>

        {{-- Event Flyer --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px;margin-bottom:16px">
            <p style="font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px">Event Flyer</p>
            <p style="font-size:12.5px;color:#9ca3af;margin-bottom:16px">Displayed on the public voting site. Recommended: 1200×630px, PNG or JPG, max 2 MB.</p>

            @if($existingFlyerPath && !$flyer)
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
                <img src="{{ asset('storage/' . $existingFlyerPath) }}" style="height:120px;width:auto;border-radius:12px;border:1px solid #e5e7eb;object-fit:cover">
                <div>
                    <p style="font-weight:600;color:#1a0030;font-size:13px;margin-bottom:4px">Current flyer</p>
                    <p style="color:#9ca3af;font-size:12px">Upload a new image below to replace it.</p>
                </div>
            </div>
            @endif

            @if($flyer)
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
                <img src="{{ $flyer->temporaryUrl() }}" style="height:120px;width:auto;border-radius:12px;border:2px solid #e91e8c;object-fit:cover">
                <div>
                    <p style="font-weight:700;color:#e91e8c;font-size:13px;margin-bottom:4px">New flyer selected</p>
                    <p style="color:#9ca3af;font-size:12px">Will be saved when you click Save.</p>
                </div>
            </div>
            @endif

            <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;border:2px dashed #e5e7eb;border-radius:12px;padding:32px;cursor:pointer;transition:all .15s"
                   onmouseover="this.style.borderColor='#e91e8c';this.style.background='#fdf4ff'"
                   onmouseout="this.style.borderColor='#e5e7eb';this.style.background=''">
                <svg style="width:32px;height:32px;color:#9ca3af;margin-bottom:10px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                <p style="font-size:13.5px;font-weight:600;color:#374151;margin-bottom:4px">
                    {{ ($existingFlyerPath && !$flyer) ? 'Click to replace flyer' : 'Click to upload event flyer' }}
                </p>
                <p style="font-size:12px;color:#9ca3af">PNG, JPG, WEBP — max 2 MB</p>
                <input wire:model="flyer" type="file" accept="image/*" style="display:none">
            </label>

            <div wire:loading wire:target="flyer" style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#e91e8c;margin-top:8px">
                <svg style="width:14px;height:14px;animation:spin 1s linear infinite" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Uploading image…
            </div>
            @error('flyer')<p style="color:#ef4444;font-size:12px;margin-top:6px">{{ $message }}</p>@enderror
        </div>

        {{-- Voting Rules --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;padding:24px;margin-bottom:24px">
            <p style="font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:20px">Voting Rules</p>

            {{-- Pay per vote toggle --}}
            <label style="display:flex;align-items:flex-start;gap:14px;cursor:pointer;margin-bottom:18px">
                <div style="position:relative;margin-top:2px;flex-shrink:0">
                    <input wire:model.live="pay_per_vote" type="checkbox" style="position:absolute;opacity:0;width:0;height:0" id="ppv">
                    <div onclick="document.getElementById('ppv').click()"
                         style="width:42px;height:24px;background:{{ $pay_per_vote ? '#e91e8c' : '#e5e7eb' }};border-radius:20px;cursor:pointer;transition:background .2s;position:relative">
                        <div style="position:absolute;top:3px;left:{{ $pay_per_vote ? '21px' : '3px' }};width:18px;height:18px;background:white;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s"></div>
                    </div>
                </div>
                <div>
                    <p style="font-size:13.5px;font-weight:600;color:#1a0030">Pay-per-vote</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Voters pay for each vote via Mobile Money or card</p>
                </div>
            </label>

            @if($pay_per_vote)
            <div style="margin-left:56px;margin-bottom:18px">
                <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Price per Vote (pesewas)</label>
                <div style="display:flex;align-items:center;gap:12px">
                    <input wire:model.live="price_per_vote_pesewas" type="number" min="0" step="10"
                           style="width:110px;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13.5px;font-family:monospace;outline:none;color:#1a0030;background:#f9fafb"
                           onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                    <div style="background:#fdf4ff;border:1px solid #e9d5ff;border-radius:10px;padding:9px 14px;font-size:13.5px">
                        = <span style="font-weight:700;color:#e91e8c">GHS {{ number_format($price_per_vote_pesewas / 100, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12.5px;font-weight:600;color:#374151;margin-bottom:6px">Max Votes per Voter</label>
                <input wire:model="max_votes_per_voter" type="number" min="1" placeholder="Unlimited"
                       style="width:110px;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 12px;font-size:13.5px;font-family:monospace;outline:none;color:#1a0030;background:#f9fafb"
                       onfocus="this.style.borderColor='#e91e8c'" onblur="this.style.borderColor='#e5e7eb'">
                <p style="font-size:12px;color:#9ca3af;margin-top:4px">Leave blank for unlimited</p>
            </div>

            {{-- Eligibility list toggle --}}
            <label style="display:flex;align-items:flex-start;gap:14px;cursor:pointer;margin-bottom:18px">
                <div style="position:relative;margin-top:2px;flex-shrink:0">
                    <input wire:model="requires_eligibility_list" type="checkbox" style="position:absolute;opacity:0;width:0;height:0" id="elig">
                    <div onclick="document.getElementById('elig').click()"
                         style="width:42px;height:24px;background:{{ $requires_eligibility_list ? '#e91e8c' : '#e5e7eb' }};border-radius:20px;cursor:pointer;transition:background .2s;position:relative">
                        <div style="position:absolute;top:3px;left:{{ $requires_eligibility_list ? '21px' : '3px' }};width:18px;height:18px;background:white;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s"></div>
                    </div>
                </div>
                <div>
                    <p style="font-size:13.5px;font-weight:600;color:#1a0030">Eligibility List Required</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Restrict voting to pre-approved members or students</p>
                </div>
            </label>

            {{-- Anonymous tally toggle --}}
            <label style="display:flex;align-items:flex-start;gap:14px;cursor:pointer">
                <div style="position:relative;margin-top:2px;flex-shrink:0">
                    <input wire:model="anonymous_tally" type="checkbox" style="position:absolute;opacity:0;width:0;height:0" id="anon">
                    <div onclick="document.getElementById('anon').click()"
                         style="width:42px;height:24px;background:{{ $anonymous_tally ? '#e91e8c' : '#e5e7eb' }};border-radius:20px;cursor:pointer;transition:background .2s;position:relative">
                        <div style="position:absolute;top:3px;left:{{ $anonymous_tally ? '21px' : '3px' }};width:18px;height:18px;background:white;border-radius:50%;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s"></div>
                    </div>
                </div>
                <div>
                    <p style="font-size:13.5px;font-weight:600;color:#1a0030">Anonymous Tally</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Voter phones anonymised after event closes — Act 843 compliant</p>
                </div>
            </label>
        </div>

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between">
            <a href="{{ route('admin.events.index') }}"
               style="font-size:13.5px;color:#9ca3af;text-decoration:none;font-weight:500">
                ← Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#2d0050,#3b0068);color:white;border:none;border-radius:12px;padding:12px 28px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(45,0,80,.3)">
                <span wire:loading.remove wire:target="save">
                    {{ $event?->exists ? 'Save Changes' : 'Create Event' }}
                </span>
                <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:8px">
                    <svg style="width:14px;height:14px;animation:spin 1s linear infinite" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Saving…
                </span>
            </button>
        </div>
    </form>

    <style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
</div>
