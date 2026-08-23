<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\ValueObjects;

use Cable8mm\NFormat\NFormat;
use JsonSerializable;
use Stringable;

/**
 * Immutable money value object returned by the AsCurrency cast.
 *
 * Echoing the object formats the value as currency, while the price(),
 * smartPrice(), and spellOut() methods expose the helpers of NFormat.
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
     * Format the amount as a currency string.
     *
     * @example (string) new Money(12346) => ₩12,346
     */
    public function __toString(): string
    {
        if ($this->value === 0 || $this->value === 0.0) {
            return $this->freeLabel();
        }

        return NFormat::currency($this->value, '0', $this->locale, $this->currency);
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
     * Alias of __toString(): format as a currency string.
     */
    public function currency(): string
    {
        return $this->__toString();
    }

    /**
     * Round the amount with the given digits.
     *
     * @example $money->price(-2) => '12300'
     */
    public function price(?int $roundDigits = null): string|false
    {
        return NFormat::price($this->value, $roundDigits);
    }

    /**
     * Smart round the amount by the currency rules.
     *
     * @example $money->smartPrice() => '12300'
     */
    public function smartPrice(): string|false
    {
        return NFormat::smartPrice($this->value, $this->currency);
    }

    /**
     * Spell out the amount as a currency string.
     *
     * @example $money->spellOut() => '12,346 원'
     */
    public function spellOut(): string
    {
        return NFormat::currencySpellOut($this->value, $this->locale, $this->currency);
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
