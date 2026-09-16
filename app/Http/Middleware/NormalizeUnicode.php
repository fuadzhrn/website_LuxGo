<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Support\Str;

/**
 * Repairs text that arrives in the wrong encoding.
 *
 * Word, Outlook and older editors write their punctuation in Windows-1252, so a
 * pasted em dash reaches us as the single byte 0x97. That byte is not valid
 * UTF-8, and once stored the browser can only render it as a replacement mark —
 * which is how "LUX&GO — Membership" became "LUX&GO ? Membership" in the page
 * title.
 *
 * Valid input is returned byte for byte, so nothing that is already correct can
 * be altered here; only bytes that are already unusable get rewritten.
 */
class NormalizeUnicode extends TransformsRequest
{
    /**
     * Attributes left alone, following TrimStrings: a password is a byte string
     * the user must be able to repeat exactly, not text to be tidied.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (! is_string($value) || $value === '' || Str::is($this->except, $key)) {
            return $value;
        }

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        /* Windows-1252 is the source in practice, and it maps every byte, so the
           conversion always succeeds rather than dropping characters. */
        return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    }
}
