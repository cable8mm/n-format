<?php

namespace Cable8mm\NFormat;

use Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver;
use Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver;
use Cable8mm\NFormat\Drivers\DriverRegistry;
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
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::SPELLOUT).
     *
     * @param  int  $number  Number not to be formatted.
     * @param  string|null  $locale  Locale override, default is NFormat::$locale.
     * @return string Formatted number
     *
     * @example NFormat::spellOut(5) => 오
     */
    public static function spellOut(int $number, ?string $locale = null): string
    {
        return static::create(
            $locale ?? static::$locale,
            NumberFormatter::SPELLOUT
        )->format($number);
    }

    /**
     * Register a custom ordinal driver for the given locale.
     *
     * @example NFormat::registerOrdinal('en_US', new EnUsOrdinalDriver)
     */
    public static function registerOrdinal(string $locale, OrdinalDriver $driver): void
    {
        DriverRegistry::registerOrdinal($locale, $driver);
    }

    /**
     * Register a custom currency driver for the given ISO 4217 code.
     *
     * @example NFormat::registerCurrency('USD', new UsdCurrencyDriver)
     */
    public static function registerCurrency(string $currency, CurrencyDriver $driver): void
    {
        DriverRegistry::registerCurrency($currency, $driver);
    }

    /**
     * Spell out ordinals for specific regions.
     *
     * @param  int  $number  Number not to be formatted.
     * @param  string|null  $locale  Locale override, default is NFormat::$locale.
     * @return string Spell out ordinal.
     *
     * @example NFormat::ordinalSpellOut(10) => 열번째
     */
    public static function ordinalSpellOut(int $number, ?string $locale = null): string
    {
        $driver = DriverRegistry::ordinal($locale ?? static::$locale);

        if (is_null($driver)) {
            return (string) $number;
        }

        return $driver->spellOut($number);
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::CURRENCY).
     *
     * @param  int|float|null  $number  Number not to be formatted
     * @param  string  $zero  If $number is 0, $zero will be returned.
     * @param  string|null  $locale  Locale override, default is NFormat::$locale.
     * @param  string|null  $currency  ISO 4217 currency code override, default is NFormat::$currency.
     *
     * @example NFormat::currency(358762) => ₩358,762
     */
    public static function currency(int|float|null $number, string $zero = '0', ?string $locale = null, ?string $currency = null): string
    {
        if (($number === 0 || $number === 0.0 || is_null($number)) && $zero !== '0') {
            return $zero;
        }

        return static::create(
            $locale ?? static::$locale,
            NumberFormatter::CURRENCY
        )->formatCurrency((float) $number, $currency ?? static::$currency);
    }

    /**
     * Spell out currency ordinals for specific regions.
     *
     * @param  int  $number  Number not to be formatted.
     * @param  string|null  $locale  Locale override, default is NFormat::$locale.
     * @param  string|null  $currency  ISO 4217 currency code override, default is NFormat::$currency.
     * @return string Spell out currency ordinal.
     *
     * @example NFormat::currencySpellOut(12346) => 12,346 원
     */
    public static function currencySpellOut(int|float $number, ?string $locale = null, ?string $currency = null): string
    {
        $currencySpellOut = static::create(
            $locale ?? static::$locale,
            NumberFormatter::EXPONENTIAL_SYMBOL
        )->formatCurrency(
            (float) $number,
            $currency ?? static::$currency
        );

        $driver = DriverRegistry::currency($currency ?? static::$currency);

        if (is_null($driver)) {
            return $currencySpellOut;
        }

        return $driver->currencySpellOut($currencySpellOut);
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::PERCENT_SYMBOL).
     *
     * @param  int  $number  Number not to be formatted
     * @param  string|null  $locale  Locale override, default is NFormat::$locale.
     *
     * @example NFormat::percent(12346) => 1,234,600%
     */
    public static function percent(int $number, ?string $locale = null): string
    {
        return static::create(
            $locale ?? static::$locale,
            NumberFormatter::PERCENT_SYMBOL
        )->format((float) $number);
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::PERCENT_SYMBOL).
     *
     * @param  int  $number  Number not to be formatted
     * @param  string|null  $locale  Locale override, defaults to NFormat::$locale.
     *
     * @example NFormat::percent(12346) => 12,346%
     */
    public static function rawPercent(int $number, ?string $locale = null): string
    {
        return static::create(
            $locale ?? static::$locale,
            NumberFormatter::PERCENT_SYMBOL
        )->format((float) ($number / 100));
    }

    /**
     * Wrapper for NumberFormatter::format($locale, NumberFormatter::DECIMAL).
     *
     * @param  int|float|null  $number  Number not to be formatted
     * @param  string  $zero  If $number is 0, $zero will be returned.
     * @param  string|null  $locale  Locale override, defaults to NFormat::$locale.
     *
     * @example NFormat::decimal(358762) => 358,762
     */
    public static function decimal(int|float|null $number, string $zero = '0', ?string $locale = null): string
    {
        if (($number === 0 || $number === 0.0 || is_null($number)) && $zero !== '0') {
            return $zero;
        }

        return static::create(
            $locale ?? static::$locale,
            NumberFormatter::DECIMAL
        )->format($number);
    }

    /**
     * Get the rounded price of a number
     *
     * @param  int|float  $number  The price
     * @param  int|null  $roundDigits  The digits of rounding number
     * @return string|false The method returns rounded number or false
     *
     * @example NFormat::price(12346, -2) => 12300
     * @example NFormat::price(12346.0) => 12346.00
     * @example NFormat::price(12346.23123, -2) => 12300.00
     */
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

    /**
     * Get the smart price of a number for shopping cart
     *
     * @param  int|float  $number  The price
     * @param  string|null  $currency  ISO 4217 currency code override, defaults to NFormat::$currency.
     * @return string|false The method returns smart rounded number or false
     *
     * @example NFormat::smartPrice(12346) => 12300
     * @example NFormat::smartPrice(1234678) => 1230000
     * @example NFormat::smartPrice(3212343232) => 3212340000
     */
    public static function smartPrice(int|float $number, ?string $currency = null): string|false
    {
        if ($number <= 0) {
            return (string) $number;
        }

        $numberOfDigits = (int) log10($number) + 1;

        $driver = DriverRegistry::currency($currency ?? static::$currency);

        if ($driver) {
            $roundDigits = $driver->roundDigits();

            if (array_key_exists($numberOfDigits, $roundDigits)) {
                return self::price($number, $roundDigits[$numberOfDigits]);
            }

            return self::price($number, end($roundDigits));
        }

        return self::price($number);
    }
}
