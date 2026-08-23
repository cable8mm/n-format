<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a spell out formatted string.
 *
 * @example Product::$casts(['count' => SpellOutCast::class])
 * @example Product::$casts(['count' => SpellOutCast::class.':ja_JP'])
 */
class SpellOutCast extends NumberCast
{
    protected function format(mixed $value): string
    {
        return NFormat::spellOut((int) $this->numeric($value), $this->locale);
    }
}
