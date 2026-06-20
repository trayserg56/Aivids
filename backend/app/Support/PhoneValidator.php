<?php

namespace App\Support;

class PhoneValidator
{
    /** @return array{digits: string, formatted: string}|null */
    public static function normalize(?string $phone): ?array
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '8') && strlen($digits) === 11) {
            $digits = '7'.substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            $digits = '7'.$digits;
        }

        if (! preg_match('/^7\d{10}$/', $digits)) {
            return null;
        }

        return [
            'digits' => $digits,
            'formatted' => '+7 ('.substr($digits, 1, 3).') '
                .substr($digits, 4, 3).'-'
                .substr($digits, 7, 2).'-'
                .substr($digits, 9, 2),
        ];
    }

    public static function isValid(?string $phone): bool
    {
        return self::normalize($phone) !== null;
    }
}
