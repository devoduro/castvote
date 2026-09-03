<?php

namespace App\Ussd\Support;

class Network
{
    /** Gateway network codes Speso accepts for a collection. */
    public const MTN = 'MTN';

    public const VODAFONE = 'VOD';

    public const AIRTELTIGO = 'ATL';

    /**
     * Resolve the network from a gateway-supplied code, falling back to the
     * number's own prefix when the gateway sends nothing useful.
     */
    public static function resolve(?string $reported, string $phone): string
    {
        $code = strtoupper(trim((string) $reported));

        $alias = [
            'MTN' => self::MTN, 'MTN-GH' => self::MTN,
            'VOD' => self::VODAFONE, 'VODAFONE' => self::VODAFONE, 'TELECEL' => self::VODAFONE,
            'ATL' => self::AIRTELTIGO, 'AIRTELTIGO' => self::AIRTELTIGO, 'TIGO' => self::AIRTELTIGO,
            'AIRTEL' => self::AIRTELTIGO,
        ];

        return $alias[$code] ?? self::fromPhone($phone);
    }

    /** Ghanaian MSISDN prefix → network. */
    public static function fromPhone(string $phone): string
    {
        $prefix = substr(PhoneNumber::normalize($phone), 0, 3);

        return match (true) {
            in_array($prefix, ['020', '050'], true)               => self::VODAFONE,
            in_array($prefix, ['026', '056', '027', '057'], true) => self::AIRTELTIGO,
            // 024, 054, 055, 059, 025, 053 — and the safe default.
            default                                               => self::MTN,
        };
    }

    /** The value stored on payments.momo_network. */
    public static function toStorage(string $network): string
    {
        return match ($network) {
            self::VODAFONE   => 'vodafone',
            self::AIRTELTIGO => 'airteltigo',
            default          => 'mtn',
        };
    }
}
