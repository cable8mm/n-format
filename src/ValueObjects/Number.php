<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\ValueObjects;

use Cable8mm\NFormat\NFormat;
use JsonSerializable;
use Stringable;

/**
 * Immutable number value object returned by the AsNumber cast.
 *
 * Echoing the object formats the value as a decimal string, while the
 * percent(), rawPercent(), spellOut(), and ordinalSpellOut() methods
 * expose the remaining helpers of NFormat.
 */
final class Number implements JsonSerializable, Stringable
{
    /**
     * @param  int|float  $value  The numeric value.
     * @param  string|null  $locale  Locale override, defaults to NFormat::$locale.
     */
    public function __construct(
        protected int|float $value,
        protected ?string $locale = null,
    ) {}

    /**
     * Format the value as a decimal string with thousand separators.
     *
     * @example (string) new Number(12346) => 12,346
     */
    public function __toString(): string
    {
        return NFormat::decimal(number: $this->value, zero: '0', locale: $this->locale);
    }

    /**
     * Format the value as a decimal string with thousand separators.
     *
     * @example $number->decimal() => 12,346
     */
    public function decimal(?string $zero = '0'): string
    {
        return NFormat::decimal(number: $this->value, zero: $zero, locale: $this->locale);
    }

    /**
     * Spell out the value as a number in words.
     *
     * @example $number->spellOut() => '일만이천삼백사십육'
     */
    public function spellOut(): string
    {
        return NFormat::spellOut(number: (int) $this->value, locale: $this->locale);
    }

    /**
     * Spell out the value as an ordinal expression.
     *
     * @example $number->ordinalSpellOut() => '열번째'
     */
    public function ordinalSpellOut(): string
    {
        return NFormat::ordinalSpellOut(number: (int) $this->value, locale: $this->locale);
    }

    /**
     * Format the value as a percentage (multiplied by 100).
     *
     * @example $number->percent() => '1,234,600%'
     */
    public function percent(): string
    {
        return NFormat::percent(number: $this->value, locale: $this->locale);
    }

    /**
     * Format the value as a raw percentage (divided by 100).
     *
     * @example $number->rawPercent() => '12,346%'
     */
    public function rawPercent(): string
    {
        return NFormat::rawPercent(number: $this->value, locale: $this->locale);
    }

    /**
     * The raw numeric value.
     */
    public function value(): int|float
    {
        return $this->value;
    }

    /**
     * JSON representation, the raw numeric value for safe calculations.
     */
    public function jsonSerialize(): int|float
    {
        return $this->value;
    }
}
