<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a raw percent formatted string.
 *
 * The stored value is divided by 100, so like NFormat::rawPercent().
 *
 * @example Item::$casts(['rate' => RawPercentCast::class])
 */
class RawPercentCast extends NumberCast
{
    protected function format(mixed $value): string
    {
        return NFormat::rawPercent((int) $this->numeric($value), $this->locale);
    }
}
