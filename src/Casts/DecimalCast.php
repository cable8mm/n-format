<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a decimal formatted string.
 *
 * @example Product::$casts(['number' => DecimalCast::class])
 * @example Product::$casts(['number' => DecimalCast::class.':ja_JP'])
 */
class DecimalCast extends NumberCast
{
    /**
     * The value to return when the number is zero.
     */
    protected string $zero;

    public function __construct(?string $locale = null, string $zero = '0')
    {
        parent::__construct($locale);

        $this->zero = $zero;
    }

    protected function format(mixed $value): string
    {
        return NFormat::decimal($this->numeric($value), $this->zero, $this->locale);
    }
}
