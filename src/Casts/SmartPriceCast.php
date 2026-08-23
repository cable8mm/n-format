<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a smart rounded price.
 *
 * @example Product::$casts => ['smart_price' => SmartPriceCast::class]
 * @example Product::$casts => ['smart_price' => SmartPriceCast::class.':JPY']
 */
class SmartPriceCast extends NumberCast
{
    public function __construct(?string $currency = null)
    {
        parent::__construct(null, $currency);
    }

    protected function format(mixed $value): string
    {
        return (string) NFormat::smartPrice($this->numeric($value), $this->currency);
    }
}
