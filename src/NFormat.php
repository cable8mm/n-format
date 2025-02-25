<?php

namespace Cable8mm\NFormat;

use NumberFormatter;

/**
 * Number formatter for not following rules like Korean and Japanese.
 */
class NFormat extends NumberFormatter
{
    /**
     * Default locale name.
     */
    public static $locale = 'ko_KR';

    /**
     * Default ISO 4217 code aka currency code.
     */
    public static $currency = 'KRW';

    /**
     * Drivers path for the ordinals.
     */
    private const ORDINAL_DRIVER_PATH = __DIR__.'/OrdinalDriver/';

    /*
    * Drivers path for the currency ordinals.
    */
    private const CURRENCY_DRIVER_PATH = __DIR__.'/CurrencyDriver/';

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::SPELLOUT).
     *
     * @param  int  $number  Number not to be formatted.
     * @return string Formatted number
     *
     * @example NFormat::spellOut(5);
     * //=> 오
     */
    public static function spellOut(int $number): string
    {
        return static::create(
            static::$locale,
            NumberFormatter::SPELLOUT
        )->format($number);
    }

    /**
     * Spell out ordinals for specific regions.
     *
     * @param  int  $number  Number not to be formatted.
     * @return string Spell out ordinal.
     *
     * @example NFormat::spellOut(10);
     * //=> 열번째
     */
    public static function ordinalSpellOut(int $number): string
    {
        $ordinalDriverPath = static::ORDINAL_DRIVER_PATH.static::$locale.'.php';

        if (file_exists($ordinalDriverPath)) {
            $ordinalCallable = require $ordinalDriverPath;

            return $ordinalCallable($number);
        }

        return $number;
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::CURRENCY).
     *
     * @param  int|float|null  $number  Number not to be formatted
     * @param  string  $zero  If $number is 0, $zero will be returned.
     *
     * @example NFormat::currency(358762);
     * //=> ₩358,762
     */
    public static function currency(int|float|null $number, string $zero = '0'): string
    {
        if (($number === 0 || $number === 0.0 || is_null($number)) && $zero !== '0') {
            return $zero;
        }

        return static::create(
            static::$locale,
            NumberFormatter::CURRENCY
        )->formatCurrency((float) $number, static::$currency);
    }

    /**
     * Spell out currency ordinals for specific regions.
     *
     * @param  int  $number  Number not to be formatted.
     * @return string Spell out currency ordinal.
     *
     * @example NFormat::spellOut(12346);
     * //=> 12,346원
     */
    public static function currencySpellOut($number): string
    {
        $currencySpellOut = static::create(
            static::$locale,
            NumberFormatter::EXPONENTIAL_SYMBOL
        )->formatCurrency(
            (float) $number,
            static::$currency
        );

        $currencyDriverPath = static::CURRENCY_DRIVER_PATH.static::$currency.'.php';

        if (file_exists($currencyDriverPath)) {
            $currencyPatterns = require $currencyDriverPath;

            if (array_key_exists('currencySpellOut', $currencyPatterns)) {
                $currencyPattern = $currencyPatterns['currencySpellOut'];

                return preg_replace(key($currencyPattern), current($currencyPattern), $currencySpellOut);
            }
        }

        return $currencySpellOut;
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::PERCENT_SYMBOL).
     *
     * @param  int  $number  Number not to be formatted
     *
     * @example NFormat::percent(12346);
     * //=> 1,234,600%
     */
    public static function percent(int $number): string
    {
        return static::create(
            static::$locale,
            NumberFormatter::PERCENT_SYMBOL
        )->format((float) $number);
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::PERCENT_SYMBOL).
     *
     * @param  int  $number  Number not to be formatted
     *
     * @example NFormat::percent(12346);
     * //=> 12,346%
     */
    public static function rawPercent(int $number): string
    {
        return static::create(
            static::$locale,
            NumberFormatter::PERCENT_SYMBOL
        )->format((float) ($number / 100));
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::DECIMAL).
     *
     * @param  int|float|null  $number  Number not to be formatted
     * @param  string  $zero  If $number is 0, $zero will be returned.
     *
     * @example NFormat::decimal(358762);
     * //=> 358,762
     */
    public static function decimal(int|float|null $number, string $zero = '0'): string
    {
        if (($number === 0 || $number === 0.0 || is_null($number)) && $zero !== '0') {
            return $zero;
        }

        return static::create(
            static::$locale,
            NumberFormatter::DECIMAL
        )->format($number);
    }

    public static function price(int|float $number, ?int $roundDigits = null): string|false
    {
        if (is_int($number)) {
            return is_null($roundDigits)
                ? round($number)
                : round($number, $roundDigits);
        }

        return is_null($roundDigits)
            ? sprintf('%.2f', $number)
            : sprintf('%.2f', round($number, $roundDigits));
    }

    public static function smartPrice(int|float $number): string|false
    {
        $numberOfDigits = (int) log10($number) + 1;

        $currencyDriverPath = static::CURRENCY_DRIVER_PATH.static::$currency.'.php';

        if (file_exists($currencyDriverPath)) {
            $currencyPatterns = require $currencyDriverPath;

            if (array_key_exists('roundDigits', $currencyPatterns)) {
                $roundDigits = $currencyPatterns['roundDigits'];

                if (array_key_exists($numberOfDigits, $roundDigits)) {
                    return self::price($number, $roundDigits[$numberOfDigits]);
                }

                return self::price($number, end($roundDigits));
            }
        }

        return self::price($number);
    }
}
