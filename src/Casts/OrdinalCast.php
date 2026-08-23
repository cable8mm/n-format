<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a spell out ordinal string.
 *
 * @example Product::$casts(['rank' => OrdinalCast::class])
 */
class OrdinalCast extends NumberCast
{
    protected function format(mixed $value): string
    {
        return NFormat::ordinalSpellOut((int) $this->numeric($value), $this->locale);
    }
}
