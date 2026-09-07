<?php

namespace App\Support;

/**
 * Turns a published Indonesian number into a dialable link.
 *
 * Numbers are written locally (0811…) and dialled internationally (+62811…),
 * so the rule lives here rather than in each place that shows one. The stored
 * number is never rewritten — only the link is built from it.
 */
class PhoneLink
{
    public static function tel(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return 'tel:+'.$digits;
    }
}
