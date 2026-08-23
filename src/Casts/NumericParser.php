<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

final class NumericParser
{
    private function __construct() {}

    public static function parse(mixed $value): int|float|null
    {
        if (! is_string($value)) {
            return is_numeric($value) ? $value + 0 : null;
        }

        $value = preg_replace('/[^0-9.+-]/', '', str_replace(',', '', $value));

        if (! is_numeric($value)) {
            return null;
        }

        return $value + 0;
    }
}
