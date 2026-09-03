<?php

namespace App\Ussd\Support;

class PhoneNumber
{
    /**
     * Normalise a Ghanaian number to the app's storage convention
     * (0XXXXXXXXX), accepting a 233-prefixed or bare local format.
     */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '233')) {
            return '0'.substr($digits, 3);
        }

        if ($digits !== '' && ! str_starts_with($digits, '0')) {
            return '0'.$digits;
        }

        return $digits;
    }

    /** Mask the middle of a number for on-screen display. */
    public static function mask(string $phone): string
    {
        $local = self::normalize($phone);

        return strlen($local) < 7
            ? $local
            : substr($local, 0, 3).str_repeat('*', strlen($local) - 6).substr($local, -3);
    }
}
