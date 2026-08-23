<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Drivers;

use Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver;
use Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver;
use Cable8mm\NFormat\Drivers\Currency\KrwCurrencyDriver;
use Cable8mm\NFormat\Drivers\Ordinal\KoKrOrdinalDriver;

/**
 * Static registry for ordinal and currency drivers.
 *
 * Built-in drivers are registered lazily on the first lookup. Custom drivers
 * can be registered explicitly for extending the library without touching the
 * package itself.
 */
class DriverRegistry
{
    /**
     * The registered ordinal drivers, keyed by locale.
     *
     * @var array<string, OrdinalDriver>
     */
    private static array $ordinalDrivers = [];

    /**
     * The registered currency drivers, keyed by ISO 4217 code.
     *
     * @var array<string, CurrencyDriver>
     */
    private static array $currencyDrivers = [];

    /**
     * Whether the built-in drivers have been registered.
     */
    private static bool $initialized = false;

    /**
     * Register the built-in drivers.
     */
    private static function boot(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$ordinalDrivers['ko_KR'] ??= new KoKrOrdinalDriver;
        self::$currencyDrivers['KRW'] ??= new KrwCurrencyDriver;

        self::$initialized = true;
    }

    /**
     * Register an ordinal driver for the given locale.
     */
    public static function registerOrdinal(string $locale, OrdinalDriver $driver): void
    {
        self::$ordinalDrivers[$locale] = $driver;
    }

    /**
     * Register a currency driver for the given ISO 4217 code.
     */
    public static function registerCurrency(string $currency, CurrencyDriver $driver): void
    {
        self::$currencyDrivers[$currency] = $driver;
    }

    /**
     * Get the ordinal driver for the given locale, or null when not registered.
     */
    public static function ordinal(string $locale): ?OrdinalDriver
    {
        self::boot();

        return self::$ordinalDrivers[$locale] ?? null;
    }

    /**
     * Get the currency driver for the given ISO 4217 code, or null when not registered.
     */
    public static function currency(string $currency): ?CurrencyDriver
    {
        self::boot();

        return self::$currencyDrivers[$currency] ?? null;
    }

    /**
     * Forget every registered driver, so that the next call re-registers the defaults.
     */
    public static function reset(): void
    {
        self::$ordinalDrivers = [];
        self::$currencyDrivers = [];

        self::$initialized = false;
    }
}
