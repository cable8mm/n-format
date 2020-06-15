<?php

namespace Cable8mm\NFormat;

use NumberFormatter;

class NFormat extends NumberFormatter
{
    public static $locale = 'ko_KR';
    public static $currency = 'KRW';

    public static function spellOut(int $number): string
    {
        return static::create(static::$locale, NumberFormatter::SPELLOUT)->format($number);
    }

    public static function ordinalSpellOut(int $number): string
    {
        $ordinalDriverPath = __DIR__ . '/OrdinalDriver/' . static::$locale . '.php';

        if (file_exists($ordinalDriverPath)) {
            $ordinalCallable = require $ordinalDriverPath;

            return $ordinalCallable($number);
        }

        return $number;
    }
}
