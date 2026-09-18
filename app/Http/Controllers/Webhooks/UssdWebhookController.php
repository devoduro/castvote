<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\UssdSession;
use App\Ussd\Responses\GatewayResponse;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Campaign;
use App\Ussd\Support\Flow;
use App\Ussd\Support\GatewayLog;
use App\Ussd\Support\Network;
use App\Ussd\Support\PhoneNumber;
use App\Ussd\Support\UssdSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Sparors\Ussd\Facades\Ussd;
use Sparors\Ussd\Record;

/**
 * USSD entry point.
 *
 * Nalo is the production gateway. Its contract (per the Nalo USSD API docs):
 *
 *   IN  { USERID, MSISDN, USERDATA, MSGTYPE, NETWORK, SESSIONID }
 *         MSGTYPE true  = first request of a new session
 *         MSGTYPE false = a subsequent screen
 *   OUT { USERID, MSISDN, USERDATA, MSG, MSGTYPE }
 *         MSGTYPE true  = keep the session open
 *         MSGTYPE false = terminate
 *
 * Speso / Africa's Talking style payloads (session_id / msisdn / input) are
 * also accepted so a different aggregator can be pointed here unchanged; keys
 * are read case-insensitively and the reply is shaped to match.
 */
class UssdWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        if (UssdSettings::debugLogging()) {
            Log::info('USSD webhook payload', ['payload' => $request->all()]);
        }

        $data = array_change_key_case($request->all(), CASE_LOWER);

        $isNalo = isset($data['userid']) || isset($data['userdata']) || isset($data['msgtype']);

        $rawMsisdn = (string) ($data['msisdn'] ?? $data['phonenumber'] ?? '');
        $phone     = PhoneNumber::normalize($rawMsisdn);
        $input     = trim((string) ($data['userdata'] ?? $data['input'] ?? $data['text'] ?? ''));
        $network   = Network::resolve($data['network'] ?? null, $phone);
        $userId    = (string) ($data['userid'] ?? '');

        // SESSIONID is the session key. USERID must never be used for this —
        // Nalo issues one USERID per integration, so every caller on the
        // platform would collide into a single shared session.
        $sessionId = (string) ($data['sessionid'] ?? $data['session_id'] ?? '');
        if ($sessionId === '') {
            // Nalo's own sample keys the session off MSISDN when SESSIONID is
            // absent; a caller can only hold one USSD session at a time.
            $sessionId = $phone !== '' ? 'msisdn-'.$phone : (string) Str::uuid();
        }

        $format = UssdSettings::responseFormat();
        if ($format === 'auto') {
            $format = $isNalo ? 'nalo' : 'speso';
        }

        $response = new GatewayResponse($format, $sessionId, $rawMsisdn, $input, $userId);
        $reply = function (string $message, string $action) use ($response) {
            $built = $response->build($message, $action);

            return is_array($built) ? response()->json($built) : response($built);
        };

        if ($phone === '') {
            Log::warning('USSD webhook missing msisdn', ['payload' => $request->all()]);
            GatewayLog::record($data, 'rejected: no MSISDN in payload');

            return $reply('Service temporarily unavailable. Please try again shortly.', 'prompt');
        }

        // USERID check. Nalo documents USERID as "the ID provided by NALO to
        // the client", but does not say whether that is the extension code or
        // an account identifier — so a mismatch is only *logged* unless strict
        // mode is on. Blocking on a guessed value would reject every real
        // dial with MSGTYPE:false on the first request, which the gateway
        // reports to the handset as an invalid account. Turn strict mode on
        // once the "What the gateway sent" panel shows the real USERID.
        $expectedUserId = (string) config('services.nalo.user_id', '');
        if ($isNalo && $expectedUserId !== '' && ! hash_equals($expectedUserId, $userId)) {
            $strict = (bool) config('services.nalo.strict_user_id', false);

            Log::warning('USSD USERID mismatch'.($strict ? ' — rejected' : ' — allowed (strict mode off)'), [
                'received' => $userId,
                'expected' => $expectedUserId,
            ]);

            if ($strict) {
                GatewayLog::record($data, 'rejected: USERID "'.$userId.'" != expected "'.$expectedUserId.'" (strict)');

                return $reply('Service unavailable.', 'prompt');
            }

            GatewayLog::record($data, 'USERID "'.$userId.'" != configured "'.$expectedUserId.'" — allowed; set NALO_USER_ID to this value');
        }

        // Kill switch from the superadmin USSD Manager.
        if (! UssdSettings::enabled()) {
            return $reply(UssdSettings::offlineMessage(), 'prompt');
        }

        $store  = config('ussd.cache_store') ?: config('cache.default');
        $record = new Record(Cache::store($store), $sessionId);

        // A fresh dial starts clean, so a reused session id can never drop the
        // caller into the middle of somebody else's half-finished flow.
        // Nalo signals this with MSGTYPE true on the first request.
        if ($this->isNewSession($data, $record)) {
            Flow::resetSession($record);
        }

        // Nalo sends no service code, so the campaign is resolved from the
        // integration's own configuration — see Campaign::resolve().
        if (! $record->has('event_id')) {
            $resolution = Campaign::resolve($data, $userId, $input);

            if ($resolution->event) {
                $record->set('event_id', $resolution->event->id);
                $this->openSession($sessionId, $resolution->event->id, $phone);
            } elseif (! $resolution->needsChoice) {
                Log::warning('USSD dial with no reachable campaign', ['userid' => $userId]);
                GatewayLog::record($data, 'reached the app, but no campaign is live');

                return $reply(
                    'No voting campaign is open right now. Please try again later.',
                    'prompt'
                );
            }
            // needsChoice: fall through — WelcomeState offers the picker.
        }

        [$message, $action] = Ussd::machine()
            ->setStore($store)
            ->setSessionId($sessionId)
            ->setPhoneNumber($phone)
            ->setNetwork($network)
            ->setInput($input)
            ->setInitialState(WelcomeState::class)
            ->setResponse(fn (string $m, string $a) => [$m, $a])
            ->run();

        $this->trackSession($sessionId, $record, $action, $phone);
        GatewayLog::record($data, $action === 'prompt' ? 'handled (session closed)' : 'handled (awaiting reply)');

        return $reply($message, $action);
    }

    /**
     * Whether this request opens a new session.
     *
     * Nalo: MSGTYPE true. Speso: type 'initiation'. Otherwise: no state yet.
     */
    private function isNewSession(array $data, Record $record): bool
    {
        if (array_key_exists('msgtype', $data)) {
            return filter_var($data['msgtype'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
        }

        return strtolower((string) ($data['type'] ?? '')) === 'initiation'
            || ! $record->has('__init');
    }

    /** Mirror the session to the database for support and audit visibility. */
    private function openSession(string $sessionId, int $eventId, string $phone): void
    {
        UssdSession::updateOrCreate(
            ['arkesel_session_id' => $sessionId],
            [
                'event_id'     => $eventId,
                'phone_number' => $phone,
                'current_step' => 'welcome',
                'payload'      => [],
                'status'       => 'active',
            ],
        );
    }

    private function trackSession(string $sessionId, Record $record, string $action, string $phone): void
    {
        UssdSession::updateOrCreate(
            ['arkesel_session_id' => $sessionId],
            [
                'event_id'     => $record->get('event_id'),
                'phone_number' => $phone,
                'current_step' => class_basename((string) $record->get('__active', 'welcome')),
                'payload'      => array_filter([
                    'category_id' => $record->get('category_id'),
                    'nominee_id'  => $record->get('nominee_id'),
                    'quantity'    => $record->get('quantity'),
                ]),
                // 'prompt' means the state was terminal, so the gateway closes it.
                'status'       => $action === 'prompt' ? 'completed' : 'active',
            ],
        );
    }
}
