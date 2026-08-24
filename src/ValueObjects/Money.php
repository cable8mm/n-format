<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\ValueObjects;

use Cable8mm\NFormat\NFormat;
use JsonSerializable;
use Stringable;

/**
 * Immutable money value object returned by the AsCurrency cast.
 *
 * Echoing the object returns the raw numeric value, while currency(),
 * price(), smartPrice(), and spellOut() expose formatted helpers.
 */
final class Money implements JsonSerializable, Stringable
{
    /**
     * @param  int|float  $value  The numeric amount.
     * @param  string|null  $locale  Locale override, defaults to NFormat::$locale.
     * @param  string|null  $currency  ISO 4217 currency code override, defaults to NFormat::$currency.
     */
    public function __construct(
        protected int|float $value,
        protected ?string $locale = null,
        protected ?string $currency = null,
    ) {}

    /**
     * Return the raw amount as a string.
     *
     * Keeping the default string representation raw avoids surprising
     * formatting when the value is passed to another package or client-side
     * calculation.
     *
     * @example (string) new Money(12346) => '12346'
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Return the translated label for a zero amount.
     */
    protected function freeLabel(): string
    {
        if (! function_exists('trans')) {
            return '무료';
        }

        return (string) trans('n-format::messages.free', [], $this->locale ?? NFormat::$locale);
    }

    /**
     * Format the amount as a currency string.
     */
    public function currency(): string
    {
        if ($this->value === 0 || $this->value === 0.0) {
            return $this->freeLabel();
        }

        return NFormat::currency(
            number: $this->value,
            zero: '0',
            locale: $this->locale,
            currency: $this->currency,
        );
    }

    /**
     * Round the amount with the given digits.
     *
     * @example $money->price(-2) => '12300'
     */
    public function price(?int $roundDigits = null): string|false
    {
        return NFormat::price(number: $this->value, roundDigits: $roundDigits);
    }

    /**
     * Smart round the amount by the currency rules.
     *
     * @example $money->smartPrice() => '12300'
     */
    public function smartPrice(): string|false
    {
        return NFormat::smartPrice(number: $this->value, currency: $this->currency);
    }

    /**
     * Spell out the amount as a currency string.
     *
     * @example $money->spellOut() => '12,346 원'
     */
    public function spellOut(): string
    {
        return NFormat::currencySpellOut(
            number: $this->value,
            locale: $this->locale,
            currency: $this->currency,
        );
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
