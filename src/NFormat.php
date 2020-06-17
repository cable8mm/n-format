<?php

namespace Cable8mm\NFormat;

use NumberFormatter;

class NFormat extends NumberFormatter
{
    public static $locale = 'ko_KR';
    public static $currency = 'KRW';

    private const ORDINAL_DRIVER_PATH = __DIR__ . '/OrdinalDriver/';
    private const CURRENCY_DRIVER_PATH = __DIR__ . '/CurrencyDriver/';

    public static function spellOut(int $number): string
    {
        return static::create(static::$locale, NumberFormatter::SPELLOUT)->format($number);
    }

    public static function ordinalSpellOut(int $number): string
    {
        $ordinalDriverPath = static::ORDINAL_DRIVER_PATH . static::$locale . '.php';

        if (file_exists($ordinalDriverPath)) {
            $ordinalCallable = require $ordinalDriverPath;

            return $ordinalCallable($number);
        }

        return $number;
    }

    /**
     * @param int|float $number
     */
    public static function currency($number): string
    {
        return static::create(static::$locale, NumberFormatter::CURRENCY)->formatCurrency((float)$number, static::$currency);
    }

    public static function currencySpellOut($number): string
    {
        $currencySpellOut = static::create(
            static::$locale,
            NumberFormatter::EXPONENTIAL_SYMBOL
        )->formatCurrency(
            (float)$number,
            static::$currency
        );

        $currencyDriverPath = static::CURRENCY_DRIVER_PATH . static::$locale . '.php';

        if (file_exists($currencyDriverPath)) {
            $currencyPatterns = require $currencyDriverPath;

            if (array_key_exists('currencySpellOut', $currencyPatterns)) {
                $currencyPattern = $currencyPatterns['currencySpellOut'];

                return preg_replace(key($currencyPattern), current($currencyPattern), $currencySpellOut);
            }
        }

        return $currencySpellOut;
    }
}
