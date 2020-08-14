<?php

namespace App\Utils;

class PriceHelper
{
    public static function numbersFromText(string $text): string
    {
        if (!$text) {
            return '';
        }

        preg_match_all(config('pcn.regex.price'), $text, $matches);

        return $matches ? str_replace(' ', '', str_replace(',', '', $matches[0][0])) : '';
    }
}
