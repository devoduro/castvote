<div class="max-w-5xl" wire:poll.30s>

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[22px] font-extrabold text-ink-900">USSD Manager</h1>
            <p class="text-[13.5px] text-ink-400 mt-1">
                Service settings, shortcode routing and a live simulator for the voting menu.
            </p>
        </div>
        <span class="badge {{ $enabled ? '' : '' }}"
              style="{{ $enabled ? 'background:#e7f8ef;color:#0c7f47' : 'background:#fee2e2;color:#b91c1c' }}">
            <span class="w-1.5 h-1.5 rounded-full bg-current {{ $enabled ? 'live-dot' : '' }}"></span>
            {{ $enabled ? 'Service online' : 'Service offline' }}
        </span>
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat label="Active sessions" :value="number_format($this->stats['active'])" icon="mobile" tone="brand" />
        <x-ui.stat label="Dials today" :value="number_format($this->stats['today'])" icon="phone" tone="violet" />
        <x-ui.stat label="USSD votes today" :value="number_format($this->stats['votesToday'])" icon="check-circle" tone="success" />
        <x-ui.stat label="Payments confirming" :value="number_format($this->stats['pendingSpeso'])" icon="clock" tone="gold" />
    </div>

    <div class="grid lg:grid-cols-[1.15fr_1fr] gap-5 items-start">

        {{-- ══ Settings ══ --}}
        <form wire:submit="save" class="card p-5 sm:p-6">
            <h2 class="text-[11px] font-extrabold uppercase tracking-[.1em] text-ink-400 mb-5">Service settings</h2>

            <div class="flex flex-col gap-5">
                <x-ui.toggle model="enabled" live :checked="$enabled"
                             label="USSD voting enabled"
                             hint="Turn off to take the shortcode down without removing the gateway route. Callers get the offline message below." />

                <div>
                    <label for="u-shortcode" class="label">Default shortcode</label>
                    <input id="u-shortcode" wire:model="shortcode" type="text" placeholder="*928#"
                           class="input font-mono @error('shortcode') is-error @enderror">
                    @error('shortcode')
                        <p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                    @else
                        <p class="hint">Shown across the public site when a campaign has no shortcode of its own.</p>
                    @enderror
                </div>

                <div>
                    <label for="u-format" class="label">Gateway response format</label>
                    <select id="u-format" wire:model="responseFormat" class="input">
                        @foreach(\App\Ussd\Support\UssdSettings::FORMATS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="hint">
                        Gateways disagree on the keyword that keeps a session open. If yours hangs up after the
                        first screen, try another contract here — no redeploy needed.
                    </p>
                </div>

                <div>
                    <label for="u-ttl" class="label">Session timeout (seconds)</label>
                    <input id="u-ttl" wire:model="sessionTtl" type="number" min="60" max="3600" step="30"
                           class="input font-mono @error('sessionTtl') is-error @enderror" style="width:140px">
                    @error('sessionTtl')
                        <p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                    @else
                        <p class="hint">How long a caller's progress is held between steps.</p>
                    @enderror
                </div>

                <div>
                    <label for="u-offline" class="label">Offline message</label>
                    <textarea id="u-offline" wire:model="offlineMessage" rows="2" maxlength="160"
                              class="input @error('offlineMessage') is-error @enderror"></textarea>
                    @error('offlineMessage')
                        <p class="error-msg"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                    @else
                        <p class="hint">Shown to callers while the service is switched off. Keep it under 160 characters.</p>
                    @enderror
                </div>

                <x-ui.toggle model="debugLogging" live :checked="$debugLogging"
                             label="Log gateway payloads"
                             hint="Writes every inbound request to the Laravel log. Useful while wiring up a new gateway; turn it off once live." />
            </div>

            <div class="flex justify-end pt-5 mt-5 border-t border-ink-100">
                <x-ui.btn type="submit" loading="save" variant="primary">Save settings</x-ui.btn>
            </div>
        </form>

        {{-- ══ Simulator ══ --}}
        <div class="card p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <h2 class="text-[11px] font-extrabold uppercase tracking-[.1em] text-ink-400">Simulator</h2>
                <button type="button" wire:click="restartSim" class="btn btn-ghost btn-sm">
                    <x-ui.icon name="refresh" :size="14" /> Restart
                </button>
            </div>

            <div class="grid sm:grid-cols-2 gap-3 mb-4">
                <div>
                    <label for="sim-code" class="label">Dial</label>
                    <input id="sim-code" wire:model="simShortcode" type="text" class="input font-mono" placeholder="*928*240#">
                </div>
                <div>
                    <label for="sim-phone" class="label">From</label>
                    <input id="sim-phone" wire:model="simPhone" type="tel" class="input font-mono" placeholder="0244123456">
                </div>
            </div>

            {{-- Handset --}}
            <div class="rounded-2xl p-4 min-h-[240px] flex flex-col gap-3 font-mono text-[12.5px] leading-relaxed"
                 style="background:#14031f;color:#d7cfe4" aria-live="polite">
                @forelse($simTranscript as $turn)
                    @if($turn['input'] !== '')
                        <p class="text-right"><span class="inline-block rounded-lg px-2.5 py-1"
                              style="background:rgba(225,29,116,.25);color:#fda4c7">{{ $turn['input'] }}</span></p>
                    @endif
                    <div>
                        <p class="whitespace-pre-wrap">{{ $turn['message'] }}</p>
                        <p class="text-[10.5px] mt-1.5 uppercase tracking-wider"
                           style="color:{{ $turn['action'] === 'prompt' ? '#f87171' : '#4ade80' }}">
                            {{ $turn['action'] === 'prompt' ? 'session ended' : 'awaiting input' }}
                        </p>
                    </div>
                @empty
                    <p class="m-auto text-center" style="color:rgba(255,255,255,.35)">
                        Press Restart to dial the shortcode and walk the menu.
                    </p>
                @endforelse
            </div>

            <form wire:submit="sendSimInput" class="flex gap-2 mt-3">
                <label for="sim-input" class="sr-only">Reply</label>
                <input id="sim-input" wire:model="simInput" type="text" class="input font-mono flex-1"
                       placeholder="Type a reply, e.g. 1" @disabled($simSession === '')>
                <x-ui.btn type="submit" loading="sendSimInput" variant="primary"
                          :disabled="$simSession === ''">Send</x-ui.btn>
            </form>
            <p class="hint">Runs the real state machine in-process — the same code the gateway hits.</p>
        </div>
    </div>

    {{-- ══ Shortcode routing ══ --}}
    <div class="card overflow-hidden mt-5">
        <div class="px-5 sm:px-6 py-4 border-b border-ink-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-[15px] font-extrabold text-ink-900">Shortcode routing</h2>
                <p class="text-[12.5px] text-ink-400 mt-0.5">
                    A dialled code resolves to a campaign by its short ID. Set one on the event to make it reachable.
                </p>
            </div>
            <span class="badge" style="background:#f4f2f8;color:#5b5470">
                Callback: <span class="font-mono ml-1">{{ url('/api/ussd/callback') }}</span>
            </span>
        </div>

        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#faf9fc">
                    @foreach(['Campaign', 'Short ID', 'Dial string', 'Status'] as $th)
                        <th style="padding:11px 20px;text-align:left;font-size:11px;font-weight:700;color:#8b849c;text-transform:uppercase;letter-spacing:.06em">
                            {{ $th }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($this->routes as $route)
                    <tr style="border-top:1px solid #f1eff6">
                        <td style="padding:12px 20px">
                            <a href="{{ route('admin.events.edit', $route->event) }}"
                               class="text-[13.5px] font-bold text-ink-900 hover:text-brand-700 transition">
                                {{ $route->event->name }}
                            </a>
                            <p class="text-[11.5px] text-ink-400 mt-0.5">{{ $route->event->organization?->name }}</p>
                        </td>
                        <td style="padding:12px 20px" class="font-mono text-[13px] text-ink-700">
                            {{ $route->shortId ?: '—' }}
                        </td>
                        <td style="padding:12px 20px" class="font-mono text-[13px] text-ink-700">
                            {{ $route->shortcode ?: ($route->shortId ? '*928*'.$route->shortId.'#' : '—') }}
                        </td>
                        <td style="padding:12px 20px">
                            @if($route->reachable)
                                <x-ui.badge tone="live" dot>Reachable</x-ui.badge>
                            @elseif(blank($route->shortId))
                                <x-ui.badge tone="warning">No short ID</x-ui.badge>
                            @else
                                <x-ui.badge tone="closed">Voting closed</x-ui.badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:0">
                            <x-ui.empty icon="mobile" title="No campaigns to route"
                                        message="Publish a campaign and give it a USSD short ID to make it dialable." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ Recent sessions ══ --}}
    <div class="card overflow-hidden mt-5">
        <h2 class="px-5 sm:px-6 py-4 border-b border-ink-100 text-[15px] font-extrabold text-ink-900">
            Recent sessions
        </h2>

        @if($this->sessions->isEmpty())
            <x-ui.empty icon="clock" title="No USSD sessions yet"
                        message="Sessions appear here as soon as callers start dialling the shortcode." />
        @else
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:#faf9fc">
                        @foreach(['Phone', 'Campaign', 'Step', 'Status', 'Started'] as $th)
                            <th style="padding:11px 20px;text-align:left;font-size:11px;font-weight:700;color:#8b849c;text-transform:uppercase;letter-spacing:.06em">
                                {{ $th }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->sessions as $session)
                        <tr style="border-top:1px solid #f1eff6">
                            <td style="padding:12px 20px" class="font-mono text-[13px] text-ink-700">
                                {{ \App\Ussd\Support\PhoneNumber::mask($session->phone_number ?: '') ?: '—' }}
                            </td>
                            <td style="padding:12px 20px" class="text-[13px] text-ink-700">
                                {{ $session->event?->name ?? '—' }}
                            </td>
                            <td style="padding:12px 20px" class="text-[13px] text-ink-500">
                                {{ Str::headline(str_replace('State', '', (string) $session->current_step)) }}
                            </td>
                            <td style="padding:12px 20px">
                                <x-ui.badge :tone="match($session->status) {
                                    'active' => 'live', 'completed' => 'success', default => 'closed',
                                }">{{ ucfirst($session->status) }}</x-ui.badge>
                            </td>
                            <td style="padding:12px 20px" class="text-[12.5px] text-ink-400">
                                {{ $session->created_at?->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
