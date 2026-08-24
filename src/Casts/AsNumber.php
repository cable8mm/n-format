<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Casts;

use Cable8mm\NFormat\ValueObjects\Number;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Casts a model attribute to a Number value object.
 *
 * Reading an attribute returns a Number instance that formats through
 * NFormat, while writing always stores the plain numeric value so that
 * the database stays quantitative.
 *
 * @example Product::$casts => ['count' => AsNumber::class]
 * @example Product::$casts => ['count' => AsNumber::class.':ja_JP']
 */
final class AsNumber implements CastsAttributes
{
    /**
     * The locale override, falls back to NFormat::$locale.
     */
    protected ?string $locale;

    public function __construct(?string $locale = null)
    {
        $this->locale = $locale;
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Number
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $number = NumericParser::parse($value);

        return is_null($number)
            ? null
            : new Number(value: $number, locale: $this->locale);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, int|float|null>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value instanceof Number) {
            return [$key => $value->value()];
        }

        if (is_null($value) || $value === '') {
            return [$key => null];
        }

        $number = NumericParser::parse($value);

        if (is_null($number)) {
            return [$key => null];
        }

        return [$key => $number];
    }
}
