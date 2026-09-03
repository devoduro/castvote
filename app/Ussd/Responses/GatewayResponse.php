<?php

namespace App\Ussd\Responses;

/**
 * Shapes the reply for whichever gateway is in front of us.
 *
 * The package's own default is `{ message, action: prompt|input }`, but Speso
 * USSD Extension configurations differ on the keyword that means "keep the
 * session open", and a passthrough extension wants the aggregator's native
 * JSON instead. Selecting the shape from config lets the contract be pinned
 * without a code change.
 *
 * Note the package hands us the *state's* action — 'input' when the state
 * expects a reply, 'prompt' when it is terminal — which is the inverse of
 * what most gateways call things. `$terminating` below normalises that.
 */
class GatewayResponse
{
    public function __construct(
        private readonly string $format,
        private readonly string $sessionId = '',
        private readonly string $msisdn = '',
        private readonly string $input = '',
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

        return match ($this->format) {
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
            'nalo' => [
                'USERID'   => $this->sessionId,
                'MSISDN'   => $this->msisdn,
                'USERDATA' => $this->input,
                'MSG'      => $message,
                'MSGTYPE'  => ! $terminating,
            ],
            // 'speso' (default): the package's documented contract.
            default => [
                'message' => $message,
                'action'  => $terminating ? 'end' : 'prompt',
            ],
        };
    }
}
