<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a percent formatted string.
 *
 * The stored value is multiplied by 100, just like NFormat::percent().
 *
 * @example Product::$casts(['discount' => PercentCast::class])
 */
class PercentCast extends NumberCast
{
    protected function format(mixed $value): string
    {
        return NFormat::percent((int) $this->numeric($value), $this->locale);
    }
}
