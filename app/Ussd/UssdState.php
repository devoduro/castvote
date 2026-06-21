<?php

namespace App\Ussd;

/**
 * Value object representing the current USSD session state loaded from Redis.
 */
class UssdState
{
    public function __construct(
        public readonly string  $sessionId,
        public readonly string  $serviceCode,
        public readonly ?int    $eventId,
        public string           $step,
        public array            $data,   // accumulated: category_code, nominee_id, quantity, etc.
    ) {}

    public static function fresh(string $sessionId, string $serviceCode, ?int $eventId): self
    {
        return new self(
            sessionId:   $sessionId,
            serviceCode: $serviceCode,
            eventId:     $eventId,
            step:        'welcome',
            data:        [],
        );
    }

    public function toArray(): array
    {
        return [
            'sessionId'   => $this->sessionId,
            'serviceCode' => $this->serviceCode,
            'eventId'     => $this->eventId,
            'step'        => $this->step,
            'data'        => $this->data,
        ];
    }
}
