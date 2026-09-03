<?php

namespace App\Ussd\States;

use App\Ussd\Support\Text;
use Sparors\Ussd\State;

/**
 * Generic terminal screen: renders whatever the preceding action stored in
 * 'final_message' and closes the session. Reused by every end-of-flow
 * outcome — vote placed, payment unreachable, cancelled, ineligible.
 */
class MessageState extends State
{
    protected $action = self::PROMPT;

    protected function beforeRendering(): void
    {
        $this->menu->text(Text::gsm((string) $this->record->get('final_message', 'Thank you for using CastVote.')));
    }

    protected function afterRendering(string $argument): void
    {
        // Terminal — the gateway closes the session after a PROMPT.
    }
}
