<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Drivers\Currency;

use Cable8mm\NFormat\Drivers\Contracts\CurrencyDriver;

/**
 * Korean Won currency driver settings.
 */
final class KrwCurrencyDriver implements CurrencyDriver
{
    public function currencySpellOut(string $formatted): string
    {
        return (string) preg_replace('/대한민국\s/u', '', $formatted);
    }

    public function roundDigits(): array
    {
        return [
            1 => 0,
            2 => 0,
            3 => -1,
            4 => -2,
            5 => -2,
            6 => -3,
            7 => -4,
        ];
    }
}
