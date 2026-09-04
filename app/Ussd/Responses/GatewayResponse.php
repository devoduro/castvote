<?php

namespace App\Ussd\Responses;

/**
 * Shapes the reply for whichever gateway is in front of us.
 *
 * Nalo is the production gateway (see the USSD API docs): it posts
 * USERID / MSISDN / USERDATA / MSGTYPE / NETWORK / SESSIONID and expects
 * USERID / MSISDN / MSG / MSGTYPE back, where MSGTYPE true means "keep the
 * session open". The other shapes are kept so a different aggregator can be
 * swapped in from the USSD Manager without a code change.
 *
 * Note the package hands us the *state's* action — 'input' when the state
 * expects a reply, 'prompt' when it is terminal — which is the inverse of
 * what most gateways call things. `$terminating` below normalises that.
 */
class GatewayResponse
{
    /** Nalo caps the displayed message at 120 characters. */
    public const MAX_MESSAGE = 120;

    public function __construct(
        private readonly string $format,
        private readonly string $sessionId = '',
        private readonly string $msisdn = '',
        private readonly string $input = '',
        private readonly string $userId = '',
    ) {}

    /**
     * @param  string  $action  'input' (awaiting a reply) or 'prompt' (final).
     * @return array|string
     */
    public function build(string $message, string $action): array|string
    {
        $terminating = $action === 'prompt';

        // The package builds menus with PHP_EOL, which is CRLF on Windows.
        // Gateways expect bare LF, and a stray CR shows as a box on some
        // handsets — so normalise here, the one place every reply passes.
        $message = str_replace(["\r\n", "\r"], "\n", $message);
        $message = $this->fit($message);

        return match ($this->format) {
            // Nalo: echo the account id back, MSGTYPE true = keep session open.
            'nalo' => [
                'USERID'   => $this->userId,
                'MSISDN'   => $this->msisdn,
                'USERDATA' => $this->input,
                'MSG'      => $message,
                'MSGTYPE'  => ! $terminating,
            ],
            'speso_input' => [
                'message' => $message,
                'action'  => $terminating ? 'end' : 'input',
            ],
            'speso_continue' => [
                'message' => $message,
                'action'  => $terminating ? 'end' : 'continue',
            ],
            'speso_con' => [
                'message' => $message,
                'action'  => $terminating ? 'END' : 'CON',
            ],
            'africastalking' => ($terminating ? 'END' : 'CON').' '.$message,
            // 'speso' (default): the package's documented contract.
            default => [
                'message' => $message,
                'action'  => $terminating ? 'end' : 'prompt',
            ],
        };
    }

    /**
     * Keep the message inside the gateway's character budget.
     *
     * Truncating mid-menu would hide the very options the caller needs, so
     * drop whole trailing lines first and only hard-cut as a last resort.
     */
    private function fit(string $message): string
    {
        if (mb_strlen($message) <= self::MAX_MESSAGE) {
            return $message;
        }

        $lines = explode("\n", $message);

        while (count($lines) > 1 && mb_strlen(implode("\n", $lines)) > self::MAX_MESSAGE) {
            array_pop($lines);
        }

        $trimmed = implode("\n", $lines);

        return mb_strlen($trimmed) > self::MAX_MESSAGE
            ? mb_substr($trimmed, 0, self::MAX_MESSAGE - 1).'.'
            : $trimmed;
    }
}
