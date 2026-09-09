<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\UssdSession;
use App\Ussd\Responses\GatewayResponse;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Campaign;
use App\Ussd\Support\GatewayLog;
use App\Ussd\Support\Network;
use App\Ussd\Support\PhoneNumber;
use App\Ussd\Support\UssdSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Sparors\Ussd\Facades\Ussd;
use Sparors\Ussd\Record;
use Throwable;

/**
 * Superadmin USSD Manager: service settings, shortcode routing, live session
 * monitoring and a simulator that drives the real state machine.
 */
class UssdManager extends Component
{
    // ── Settings form ────────────────────────────────────────────────────
    public bool   $enabled          = true;
    public string $shortcode        = '';
    public string $responseFormat   = 'nalo';
    public int    $sessionTtl       = 300;
    public int    $itemsPerPage     = 3;
    public string $offlineMessage   = '';
    public bool   $debugLogging     = true;

    // ── Simulator ────────────────────────────────────────────────────────
    public string $simPhone     = '0244123456';
    public string $simShortcode = '';
    public string $simInput     = '';
    public string $simSession   = '';

    /** @var array<int, array{input: string, message: string, action: string, length: int}> */
    public array $simTranscript = [];

    /** Character budget the current gateway allows in one screen. */
    public function messageLimit(): int
    {
        return GatewayResponse::MAX_MESSAGE;
    }

    public function mount(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $this->enabled        = UssdSettings::enabled();
        $this->shortcode      = UssdSettings::shortcode();
        $this->responseFormat = UssdSettings::responseFormat();
        $this->sessionTtl     = UssdSettings::sessionTtl();
        $this->itemsPerPage   = UssdSettings::itemsPerPage();
        $this->offlineMessage = UssdSettings::offlineMessage();
        $this->debugLogging   = UssdSettings::debugLogging();

        $this->simShortcode = $this->firstRoutableShortcode() ?? $this->shortcode;
    }

    public function save(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $this->validate([
            'shortcode'      => ['required', 'string', 'max:32'],
            'responseFormat' => ['required', 'string', 'in:'.implode(',', array_keys(UssdSettings::FORMATS))],
            'sessionTtl'     => ['required', 'integer', 'min:60', 'max:3600'],
            'itemsPerPage'   => ['required', 'integer', 'min:2', 'max:8'],
            'offlineMessage' => ['required', 'string', 'max:120'],
        ], [], [
            'responseFormat' => 'response format',
            'sessionTtl'     => 'session timeout',
            'itemsPerPage'   => 'items per page',
            'offlineMessage' => 'offline message',
        ]);

        Setting::putMany([
            'ussd_enabled'         => $this->enabled,
            'ussd_shortcode'       => $this->shortcode,
            'ussd_response_format' => $this->responseFormat,
            'ussd_session_ttl'     => $this->sessionTtl,
            'ussd_items_per_page'  => $this->itemsPerPage,
            'ussd_offline_message' => $this->offlineMessage,
            'ussd_debug_logging'   => $this->debugLogging,
        ]);

        AuditLog::record('ussd.settings_updated', null, [
            'enabled'         => $this->enabled,
            'response_format' => $this->responseFormat,
        ]);

        $this->dispatch('cv-toast', type: 'success', message: 'USSD settings saved.');
    }

    // ── Shortcode routing ────────────────────────────────────────────────

    /** Campaigns that can currently be reached by dialling. */
    #[Computed]
    public function routes()
    {
        return Event::with('organization')
            ->whereIn('status', ['live', 'closed'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get()
            ->map(fn (Event $e) => (object) [
                'event'     => $e,
                'shortId'   => $e->ussd_short_id,
                'shortcode' => $e->ussd_shortcode,
                'reachable' => filled($e->ussd_short_id) && $e->status === 'live' && $e->isLive(),
            ]);
    }

    private function firstRoutableShortcode(): ?string
    {
        $event = Event::where('status', 'live')->whereNotNull('ussd_short_id')->first();

        return $event?->ussd_shortcode ?: ($event ? UssdSettings::dialString($event->ussd_short_id) : null);
    }

    // ── Live activity ────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        return [
            'active'        => UssdSession::where('status', 'active')->count(),
            'today'         => UssdSession::whereDate('created_at', today())->count(),
            'votesToday'    => (int) \App\Models\Vote::where('channel', 'ussd')->whereDate('created_at', today())->sum('quantity'),
            'pendingSpeso'  => Payment::where('provider', 'speso')->where('status', 'pending')->count(),
        ];
    }

    /** What the gateway actually sent us, most recent first. */
    #[Computed]
    public function gatewayLog(): array
    {
        return GatewayLog::recent();
    }

    public function clearGatewayLog(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        GatewayLog::clear();
        unset($this->gatewayLog);

        $this->dispatch('cv-toast', type: 'success', message: 'Gateway log cleared.');
    }

    /** The USERID this deployment expects, for the diagnostics panel. */
    public function expectedUserId(): string
    {
        return (string) config('services.nalo.user_id', '');
    }

    #[Computed]
    public function sessions()
    {
        return UssdSession::with('event')->latest()->limit(15)->get();
    }

    // ── Simulator ────────────────────────────────────────────────────────

    /**
     * Drive the real state machine in-process, exactly as the webhook does,
     * so a shortcode can be proved before a gateway is pointed at it.
     */
    public function simulate(string $input = '', bool $restart = false): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $store = config('ussd.cache_store') ?: config('cache.default');

        if ($restart || $this->simSession === '') {
            $this->simSession    = 'sim-'.Str::random(12);
            $this->simTranscript = [];
            $input               = '';
        }

        $phone  = PhoneNumber::normalize($this->simPhone);
        $record = new Record(Cache::store($store), $this->simSession);

        if ($restart) {
            $record->flush();
        }

        if (! $record->has('event_id')) {
            // Mirror production: the same resolver the webhook uses.
            $resolution = Campaign::resolve(['service_code' => $this->simShortcode], '', $input);

            if ($resolution->event) {
                $record->set('event_id', $resolution->event->id);
            } elseif (! $resolution->needsChoice) {
                $this->simTranscript[] = [
                    'input'   => $input,
                    'message' => 'No voting campaign is open right now. Please try again later.',
                    'action'  => 'prompt',
                    'length'  => 61,
                ];
                $this->simSession = '';

                return;
            }
            // needsChoice: the welcome screen becomes a campaign picker.
        }

        try {
            [$message, $action] = Ussd::machine()
                ->setStore($store)
                ->setSessionId($this->simSession)
                ->setPhoneNumber($phone)
                ->setNetwork(Network::fromPhone($phone))
                ->setInput($input)
                ->setInitialState(WelcomeState::class)
                ->setResponse(fn (string $m, string $a) => [$m, $a])
                ->run();
        } catch (Throwable $e) {
            report($e);

            $message = 'Simulator error: '.$e->getMessage();
            $action  = 'prompt';
        }

        // The gateway shapes and truncates the reply, so record what the
        // handset would actually receive — including whether it was clipped.
        $message = str_replace(["\r\n", "\r"], "\n", $message);

        $this->simTranscript[] = [
            'input'   => $input,
            'message' => $message,
            'action'  => $action,
            'length'  => mb_strlen($message),
        ];
        $this->simInput = '';

        // A terminal screen closes the session, so the next step starts fresh.
        if ($action === 'prompt') {
            $this->simSession = '';
        }
    }

    public function sendSimInput(): void
    {
        $this->simulate($this->simInput);
    }

    public function restartSim(): void
    {
        $this->simulate('', restart: true);
    }

    public function render()
    {
        return view('livewire.admin.ussd-manager');
    }
}
