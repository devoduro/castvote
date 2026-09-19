<?php

namespace App\Ussd\Support;

use App\Models\UssdSession;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Throwable;

/**
 * Walks a USSD session against this deployment's own callback and checks
 * every reply against the Nalo USSD API contract.
 *
 * Exists so that "is your endpoint working?" has a one-line answer that the
 * gateway's support staff can see for themselves: open /api/ussd/selftest in
 * a browser, or run `php artisan ussd:selftest` on the server.
 *
 * The walk goes through the real HTTP stack — routing, middleware, throttle,
 * controller, JSON response — but in-process rather than over the network.
 * A route that made HTTP calls back to its own host would tie up one FPM
 * worker waiting on another, which on shared hosting can deadlock. TLS and
 * DNS are the only things this cannot prove, and the gateway proves those by
 * reaching the page at all.
 */
class SelfTest
{
    /** Reserved caller for self-tests, so its rows are recognisable in the admin panel. */
    public const MSISDN = '233200000001';

    private const CALLBACK = '/api/ussd/callback';

    /**
     * Run the walk.
     *
     * @return array{passed: bool, steps: array<int, array<string, mixed>>, checks: int, failures: int}
     */
    public static function run(): array
    {
        $userId = (string) (config('services.nalo.user_id') ?: 'NALOTest');

        $results  = [];
        $checks   = 0;
        $failures = 0;

        $tally = function (array $result) use (&$results, &$checks, &$failures) {
            $results[] = $result;
            foreach ($result['checks'] as $check) {
                $checks++;
                if (! $check['pass']) {
                    $failures++;
                }
            }
        };

        // Always start with a fresh dial.
        $first = self::step($userId, ['label' => 'Initial dial (MSGTYPE true)', 'input' => '', 'first' => true, 'open' => true]);
        $tally($first);

        // The rest of the walk depends on what the first screen offered. A
        // campaign accepting votes shows "1. Vote"; a closed one shows only
        // "My votes"; several campaigns show a picker. Each is a valid
        // production state, and the test must pass truthfully in all of them.
        $menu = (string) ($first['msg'] ?? '');

        if (str_contains($menu, '1. Vote')) {
            $tally(self::step($userId, ['label' => 'Reply "1" — Vote, category list', 'input' => '1', 'first' => false, 'open' => true]));
            $tally(self::step($userId, ['label' => 'Reply "0" — Back to main menu',   'input' => '0', 'first' => false, 'open' => true]));
            $tally(self::step($userId, ['label' => 'Reply "0" — Exit, session ends',  'input' => '0', 'first' => false, 'open' => false]));
        } else {
            $tally(self::step($userId, ['label' => 'Reply "0" — Exit, session ends',  'input' => '0', 'first' => false, 'open' => false]));
        }

        // A reply after the closing screen must be answered, not crashed on.
        $tally(self::step($userId, ['label' => 'Stray reply after session ended (gateway retry)', 'input' => '0', 'first' => false, 'open' => false]));

        // Leave nothing behind but the gateway-log rows, which the panel
        // labels as self-test traffic. Sessions store the normalised local
        // form of the number, not the MSISDN as sent.
        UssdSession::where('phone_number', PhoneNumber::normalize(self::MSISDN))->delete();

        return [
            'passed'   => $failures === 0,
            'steps'    => $results,
            'checks'   => $checks,
            'failures' => $failures,
        ];
    }

    /** @param  array{label: string, input: string, first: bool, open: bool}  $step */
    private static function step(string $userId, array $step): array
    {
        $body = json_encode([
            'USERID'   => $userId,
            'MSISDN'   => self::MSISDN,
            'USERDATA' => $step['input'],
            'MSGTYPE'  => $step['first'],
        ], JSON_UNESCAPED_SLASHES);

        $checks = [];
        $add = function (bool $pass, string $what) use (&$checks) {
            $checks[] = ['pass' => $pass, 'what' => $what];
        };

        $started = microtime(true);

        try {
            $response = self::dispatch($body);
        } catch (Throwable $e) {
            $add(false, 'Request threw: '.$e->getMessage());

            return ['label' => $step['label'], 'checks' => $checks, 'msg' => null, 'ms' => 0];
        }

        $ms      = (int) round((microtime(true) - $started) * 1000);
        $status  = $response->getStatusCode();
        $type    = (string) $response->headers->get('Content-Type', '');
        $decoded = json_decode((string) $response->getContent(), true);

        $add($status === 200, "HTTP {$status} ({$ms} ms)");
        $add(stripos($type, 'application/json') !== false, "Content-Type: {$type}");
        $add(is_array($decoded), 'Body is valid JSON');

        if (! is_array($decoded)) {
            return ['label' => $step['label'], 'checks' => $checks, 'msg' => null, 'ms' => $ms];
        }

        $keys = array_keys($decoded);
        sort($keys);
        $msg = (string) ($decoded['MSG'] ?? '');

        $add($keys === ['MSG', 'MSGTYPE', 'MSISDN', 'USERDATA', 'USERID'], 'Keys: '.implode(', ', array_keys($decoded)));
        $add(is_bool($decoded['MSGTYPE'] ?? null), 'MSGTYPE is a boolean: '.var_export($decoded['MSGTYPE'] ?? null, true));
        $add(($decoded['MSGTYPE'] ?? null) === $step['open'], 'MSGTYPE '.($step['open'] ? 'true (session open)' : 'false (session ended)').' as expected');
        $add(($decoded['USERID'] ?? null) === $userId, 'USERID echoed back');
        $add(($decoded['MSISDN'] ?? null) === self::MSISDN, 'MSISDN echoed back');
        $add(mb_strlen($msg) <= 120, 'MSG length '.mb_strlen($msg).' / 120');
        $add(! str_contains($msg, "\r"), 'MSG uses bare line feeds');

        return ['label' => $step['label'], 'checks' => $checks, 'msg' => $msg, 'ms' => $ms];
    }

    /**
     * Push one request through the full HTTP kernel, in-process, the way the
     * test suite does — then put the original request back so the caller's
     * own response is unaffected.
     */
    private static function dispatch(string $body)
    {
        $app     = app();
        $outer   = $app->bound('request') ? $app['request'] : null;

        $request = Request::create(
            self::CALLBACK, 'POST', [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json', 'REMOTE_ADDR' => '127.0.0.1'],
            $body
        );

        try {
            return $app->make(Kernel::class)->handle($request);
        } finally {
            if ($outer) {
                $app->instance('request', $outer);
            }
        }
    }

    /** Render a run as the plain-text report shown at /api/ussd/selftest. */
    public static function report(array $run): string
    {
        $endpoint = url(self::CALLBACK);
        $userId   = (string) (config('services.nalo.user_id') ?: 'NALOTest');
        $rule     = str_repeat('=', 72);

        $out  = "{$rule}\n";
        $out .= "ClickVote USSD endpoint self-test\n";
        $out .= "Endpoint : {$endpoint}\n";
        $out .= "USERID   : {$userId}\n";
        $out .= "MSISDN   : ".self::MSISDN."  (reserved self-test number)\n";
        $out .= "Time     : ".gmdate('Y-m-d H:i:s')." UTC\n";
        $out .= "{$rule}\n\n";

        foreach ($run['steps'] as $i => $step) {
            $out .= '['.($i + 1).'/'.count($run['steps'])."] {$step['label']}\n";

            foreach ($step['checks'] as $check) {
                $out .= '      '.($check['pass'] ? 'ok  ' : 'FAIL')."  {$check['what']}\n";
            }

            if ($step['msg'] !== null) {
                $out .= "      ---- MSG as the handset would show it ----\n";
                foreach (explode("\n", $step['msg']) as $line) {
                    $out .= "      | {$line}\n";
                }
                $out .= "      ------------------------------------------\n";
            }

            $out .= "\n";
        }

        $out .= "{$rule}\n";
        $out .= $run['passed']
            ? "RESULT: PASS — {$run['checks']} checks, all screens conform to the Nalo USSD API contract.\n"
            : "RESULT: FAIL — {$run['failures']} of {$run['checks']} checks failed. See above.\n";
        $out .= "{$rule}\n";

        return $out;
    }
}
