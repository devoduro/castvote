<?php

namespace App\Ussd;

/**
 * Value object returned by every menu handler.
 * isFinal=true  → respond with "END ..."
 * isFinal=false → respond with "CON ..."
 */
class UssdResponse
{
    public function __construct(
        public readonly string $text,
        public readonly bool   $isFinal,
    ) {}

    public static function continue(string $text): self
    {
        return new self(text: $text, isFinal: false);
    }

    public static function end(string $text): self
    {
        return new self(text: $text, isFinal: true);
    }
}
