<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\UssdSession;
use App\Ussd\Responses\GatewayResponse;
use App\Ussd\States\WelcomeState;
use App\Ussd\Support\Flow;
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
 * USSD entry point, driven by speso/laravel-ussd v2.
 *
 * Gateways disagree on payload shape, so both the common variants are
 * normalised here:
 *   Speso / generic : { session_id, msisdn, network, input, type, service_code }
 *   Nalo passthrough: { USERID, MSISDN, USERDATA, MSGTYPE, NETWORK }
 * Keys are read case-insensitively, and the reply is shaped to match.
 */
class UssdWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        if (UssdSettings::debugLogging()) {
            Log::info('USSD webhook payload', ['payload' => $request->all()]);
        }

        $data = array_change_key_case($request->all(), CASE_LOWER);

        $isNaloShape = isset($data['userid']) || isset($data['userdata']) || isset($data['msgtype']);

        $rawMsisdn = (string) ($data['msisdn'] ?? $data['phonenumber'] ?? '');
        $phone     = PhoneNumber::normalize($rawMsisdn);
        $input     = (string) ($data['input'] ?? $data['userdata'] ?? $data['text'] ?? '');
        $network   = Network::resolve($data['network'] ?? null, $phone);

        $serviceCode = (string) ($data['service_code'] ?? $data['servicecode'] ?? $data['shortcode'] ?? '');

        $sessionId = (string) ($data['session_id'] ?? $data['sessionid'] ?? $data['userid'] ?? '');
        if ($sessionId === '') {
            $sessionId = $phone !== '' ? $phone : (string) Str::uuid();
        }

        $format = UssdSettings::responseFormat();
        if ($format === 'auto') {
            $format = $isNaloShape ? 'nalo' : 'speso';
        }

        $response = new GatewayResponse($format, $sessionId, $rawMsisdn, $input);
        $reply = function (string $message, string $action) use ($response) {
            $built = $response->build($message, $action);

            return is_array($built) ? response()->json($built) : response($built);
        };

        if ($phone === '') {
            Log::warning('USSD webhook missing msisdn', ['payload' => $request->all()]);

            return $reply('Service temporarily unavailable. Please try again shortly.', 'prompt');
        }

        // Kill switch from the superadmin USSD Manager.
        if (! UssdSettings::enabled()) {
            return $reply(UssdSettings::offlineMessage(), 'prompt');
        }

        $store  = config('ussd.cache_store') ?: config('cache.default');
        $record = new Record(Cache::store($store), $sessionId);

        // A fresh dial starts clean, so a reused session id can never drop the
        // caller into the middle of somebody else's half-finished flow.
        $isNewSession = strtolower((string) ($data['type'] ?? '')) === 'initiation'
            || (string) ($data['msgtype'] ?? '') === '1'
            || $request->boolean('newsession')
            || ! $record->has('__init');

        if ($isNewSession) {
            $record->flush();
        }

        // The campaign is resolved once, from the dialled shortcode, and then
        // carried in the record for the rest of the session.
        if (! $record->has('event_id')) {
            $event = Flow::resolveEvent($serviceCode);

            if (! $event) {
                Log::warning('USSD dial for unknown or closed shortcode', ['service_code' => $serviceCode]);

                return $reply(
                    'No voting campaign is open on this shortcode right now. Please try again later.',
                    'prompt'
                );
            }

            $record->set('event_id', $event->id);
            $this->openSession($sessionId, $event->id, $phone);
        }

        $result = Ussd::machine()
            ->setStore($store)
            ->setSessionId($sessionId)
            ->setPhoneNumber($phone)
            ->setNetwork($network)
            ->setInput($input)
            ->setInitialState(WelcomeState::class)
            ->setResponse(fn (string $message, string $action) => [$message, $action])
            ->run();

        [$message, $action] = $result;

        $this->trackSession($sessionId, $record, $action);

        return $reply($message, $action);
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

    private function trackSession(string $sessionId, Record $record, string $action): void
    {
        UssdSession::where('arkesel_session_id', $sessionId)->update([
            'current_step' => class_basename((string) $record->get('__active', 'welcome')),
            'payload'      => array_filter([
                'category_id' => $record->get('category_id'),
                'nominee_id'  => $record->get('nominee_id'),
                'quantity'    => $record->get('quantity'),
            ]),
            // 'prompt' means the state was terminal, so the gateway closes it.
            'status'       => $action === 'prompt' ? 'completed' : 'active',
        ]);
    }
}
