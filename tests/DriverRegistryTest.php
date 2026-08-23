<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver;
use Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver;
use Cable8mm\NFormat\Drivers\DriverRegistry;
use Cable8mm\NFormat\NFormat;
use PHPUnit\Framework\TestCase;

class DriverRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DriverRegistry::reset();

        NFormat::$locale = 'ko_KR';
        NFormat::$currency = 'KRW';
    }

    protected function tearDown(): void
    {
        DriverRegistry::reset();

        parent::tearDown();
    }

    public function test_default_ordinal_driver_is_registered(): void
    {
        $driver = DriverRegistry::ordinal('ko_KR');

        $this->assertInstanceOf(OrdinalDriver::class, $driver);
        $this->assertSame('첫번째', $driver->spellOut(1));
        $this->assertSame('열번째', $driver->spellOut(10));
        $this->assertSame('이십번째', $driver->spellOut(20));
    }

    public function test_default_currency_driver_is_registered(): void
    {
        $driver = DriverRegistry::currency('KRW');

        $this->assertInstanceOf(CurrencyDriver::class, $driver);
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], array_keys($driver->roundDigits()));
        $this->assertSame([0, 0, -1, -2, -2, -3, -4], array_values($driver->roundDigits()));
        $this->assertSame('12,346 원', $driver->currencySpellOut('12,346 대한민국 원'));
    }

    public function test_unregistered_driver_returns_null(): void
    {
        $this->assertNull(DriverRegistry::ordinal('en_US'));
        $this->assertNull(DriverRegistry::currency('USD'));
    }

    public function test_custom_ordinal_driver_can_be_registered(): void
    {
        $driver = new class implements OrdinalDriver
        {
            public function spellOut(int $number): string
            {
                return '#'.$number;
            }
        };

        NFormat::registerOrdinal('en_US', $driver);

        $this->assertInstanceOf(OrdinalDriver::class, DriverRegistry::ordinal('en_US'));
        $this->assertSame('#7', NFormat::ordinalSpellOut(7, 'en_US'));
    }

    public function test_custom_currency_driver_can_be_registered(): void
    {
        $driver = new class implements CurrencyDriver
        {
            public function currencySpellOut(string $formatted): string
            {
                return $formatted.' AUD';
            }

            public function roundDigits(): array
            {
                return [
                    3 => -1,
                    4 => -2,
                ];
            }
        };

        NFormat::registerCurrency('AUD', $driver);

        NFormat::$currency = 'AUD';

        $this->assertSame('12300', NFormat::smartPrice(12346));
        $this->assertSame('12345700', NFormat::smartPrice(12345678));
    }

    public function test_custom_driver_replaces_a_default_before_first_lookup(): void
    {
        $driver = new class implements CurrencyDriver
        {
            public function currencySpellOut(string $formatted): string
            {
                return 'custom_currency';
            }

            public function roundDigits(): array
            {
                return [1 => 0];
            }
        };

        NFormat::registerCurrency('KRW', $driver);

        $this->assertSame($driver, DriverRegistry::currency('KRW'));
        $this->assertSame('custom_currency', NFormat::currencySpellOut(12346));
    }

    public function test_custom_currency_driver_is_used_for_currency_spell_out(): void
    {
        $driver = new class implements CurrencyDriver
        {
            public function currencySpellOut(string $formatted): string
            {
                return 'custom_currency';
            }

            public function roundDigits(): array
            {
                return [];
            }
        };

        NFormat::$currency = 'AUD';
        NFormat::registerCurrency('AUD', $driver);

        $this->assertSame('custom_currency', NFormat::currencySpellOut(12346, 'ko_KR', 'AUD'));
    }

    public function test_reset_restores_default_drivers(): void
    {
        $custom = new class implements OrdinalDriver
        {
            public function spellOut(int $number): string
            {
                return 'custom'.$number;
            }
        };

        NFormat::registerOrdinal('en_US', $custom);

        $this->assertSame('custom5', NFormat::ordinalSpellOut(5, 'en_US'));

        DriverRegistry::reset();

        $this->assertNull(DriverRegistry::ordinal('en_US'));
        $this->assertInstanceOf(OrdinalDriver::class, DriverRegistry::ordinal('ko_KR'));
    }
}
