<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a formatted currency string.
 *
 * @example Product::$casts => ['price' => CurrencyCast::class]
 * @example Product::$casts => ['price' => CurrencyCast::class.':ja_JP,JPY']
 * @example Product::$casts => ['price' => CurrencyCast::class.':ko_KR,KRW,-']
 */
class CurrencyCast extends NumberCast
{
    /**
     * The value to return when the number is zero.
     */
    protected string $zero;

    public function __construct(?string $locale = null, ?string $currency = null, string $zero = '0')
    {
        parent::__construct($locale, $currency);

        $this->zero = $zero;
    }

    protected function format(mixed $value): string
    {
        return NFormat::currency($this->numeric($value), $this->zero, $this->locale, $this->currency);
    }
}
