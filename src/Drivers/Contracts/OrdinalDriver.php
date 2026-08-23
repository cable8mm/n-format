<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Drivers\Contracts;

/**
 * Contract for custom ordinal spell out drivers.
 */
interface OrdinalDriver
{
    /**
     * Spell out the given number as an ordinal expression.
     */
    public function spellOut(int $number): string;
}
