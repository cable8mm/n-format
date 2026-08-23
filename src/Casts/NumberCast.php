<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Base class for all NFormat eloquent casts.
 *
 * Reading an attribute returns the formatted value while writing always stores
 * the plain numeric value so that the database stays quantitative.
 */
abstract class NumberCast implements CastsAttributes
{
    /**
     * Locale override, falls back to NFormat::$locale.
     */
    protected ?string $locale = null;

    /**
     * ISO 4217 currency code override, falls back to NFormat::$currency.
     */
    protected ?string $currency = null;

    public function __construct(?string $locale = null, ?string $currency = null)
    {
        $this->locale = $locale;
        $this->currency = $currency;
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        return $this->format($value);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, int|float|null>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (is_null($value) || $value === '') {
            return [$key => null];
        }

        $number = $this->normalize($value);

        if (is_null($number)) {
            return [$key => null];
        }

        return [$key => $number];
    }

    /**
     * Convert the given value into a numeric value.
     *
     * Formatted strings such as "₩12,350" or "12,346 원" are supported.
     */
    protected function normalize(mixed $value): int|float|null
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

    /**
     * Convert the given value into a numeric value for formatting.
     */
    protected function numeric(mixed $value): int|float
    {
        return is_numeric($value) ? $value + 0 : 0;
    }

    /**
     * Convert the given numeric value into the formatted value.
     */
    abstract protected function format(mixed $value): string;
}
