<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\NFormat;

/**
 * Casts an attribute to a rounded price.
 *
 * @example Product::$casts => ['rounded' => PriceCast::class.':-2']
 */
class PriceCast extends NumberCast
{
    /**
     * The digits of rounding number.
     */
    protected ?int $roundDigits;

    public function __construct(int|string|null $roundDigits = null)
    {
        parent::__construct();

        $this->roundDigits = is_null($roundDigits) ? null : (int) $roundDigits;
    }

    protected function format(mixed $value): string
    {
        return (string) NFormat::price($this->numeric($value), $this->roundDigits);
    }
}
