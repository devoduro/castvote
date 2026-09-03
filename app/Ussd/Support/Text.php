<?php

namespace App\Ussd\Support;

class Text
{
    /**
     * Make a string safe for a USSD handset.
     *
     * Gateways encode menus as GSM-7, so characters outside that set are
     * dropped or turned into "?" on the phone. Nominee and category names
     * come from organiser input and routinely contain smart quotes, en
     * dashes and accents, so fold them to their ASCII equivalents first.
     */
    public static function gsm(string $value): string
    {
        $replacements = [
            '–' => '-', '—' => '-', '‐' => '-', '−' => '-',
            '“' => '"', '”' => '"', '„' => '"',
            '‘' => "'", '’' => "'", '‚' => "'",
            '…' => '...', '•' => '-', '·' => '-',
            'GH₵' => 'GHS', '₵' => 'GHS', '€' => 'EUR', '£' => 'GBP',
            '\u{00A0}' => ' ',
        ];

        // Explicit accent folding rather than iconv//TRANSLIT, whose output
        // varies by platform and mangles legitimate apostrophes ("couldn't").
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ɛ' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ɔ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ñ' => 'n', 'ç' => 'c', 'ý' => 'y', 'ÿ' => 'y', 'ß' => 'ss',
            'æ' => 'ae', 'ø' => 'o', 'œ' => 'oe',
        ];

        $value = strtr($value, $replacements + $accents + array_combine(
            array_map(fn ($k) => mb_strtoupper($k), array_keys($accents)),
            array_map(fn ($v) => mb_strtoupper($v), array_values($accents)),
        ));

        // Anything still outside printable ASCII would render as a box.
        $value = preg_replace('/[^\x20-\x7E\n]/', '', $value) ?? $value;

        return trim($value, " \t");
    }

    /**
     * Shorten a label so a menu row still fits a handset screen.
     */
    public static function truncate(string $value, int $limit = 28): string
    {
        $value = self::gsm($value);

        return mb_strlen($value) > $limit
            ? rtrim(mb_substr($value, 0, $limit - 1)).'.'
            : $value;
    }
}
