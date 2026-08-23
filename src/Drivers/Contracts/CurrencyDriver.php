<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Drivers\Contracts;

/**
 * Contract for custom currency drivers.
 */
interface CurrencyDriver
{
    /**
     * Transform an already formatted currency string into the locale word form.
     */
    public function currencySpellOut(string $formatted): string;

    /**
     * Smart rounding rules by number of digits, digit count => round digits.
     *
     * @return array<int, int>
     */
    public function roundDigits(): array;
}
