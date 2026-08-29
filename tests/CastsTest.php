<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\Casts\AsCurrency;
use Cable8mm\NFormat\NFormat;
use Cable8mm\NFormat\ValueObjects\Money;
use Cable8mm\NFormat\ValueObjects\Number;

class CastsTest extends TestCase
{
    public function test_cast_stores_raw_number_and_returns_money(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame(12346, $product->getAttributes()['price']);
        $this->assertInstanceOf(Money::class, $product->price);
        $this->assertSame('12346', (string) $product->price);
        $this->assertSame('₩12,346', $product->price->currency());
        $this->assertSame('₩12,346', $product->price->display());
    }

    public function test_currency_is_explicit_presentation_behavior(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame('12346', (string) $product->price);
        $this->assertSame('₩12,346', $product->price->currency());
    }

    public function test_zero_currency_is_formatted_as_currency(): void
    {
        $product = new Product;

        $product->price = 0;

        $this->assertSame('0', (string) $product->price);
        $this->assertSame('₩0', $product->price->currency());
        $this->assertSame('무료', $product->price->freeLabel());
        $this->assertSame('무료', $product->price->display());
        $this->assertSame(0, $product->price->value());
        $this->assertSame(0, $product->price->jsonSerialize());
    }

    public function test_zero_currency_uses_the_cast_locale_translation_for_display(): void
    {
        $product = new Product;

        $product->jpy = 0;

        $this->assertSame('0', (string) $product->jpy);
        $this->assertSame('￥0', $product->jpy->currency());
        $this->assertSame('無料', $product->jpy->freeLabel());
        $this->assertSame('無料', $product->jpy->display());

        $money = new Money(0, 'en');
        $this->assertSame('Free', $money->freeLabel());
        $this->assertSame('Free', $money->display());
    }

    public function test_price_and_smart_price_methods_on_money(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame('12300', $product->price->price(-2));
        $this->assertSame('12300', $product->price->smartPrice());
    }

    public function test_money_spell_out_uses_currency_spell_out(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame('12,346 원', $product->price->spellOut());
        $this->assertSame(NFormat::currencySpellOut(12346), $product->price->spellOut());
    }

    public function test_accepts_formatted_string(): void
    {
        $product = new Product;

        $product->price = '₩12,350원';

        $this->assertSame(12350, $product->getAttributes()['price']);
        $this->assertSame('12350', (string) $product->price);
    }

    public function test_cast_with_locale_and_currency(): void
    {
        $product = new Product;

        $product->jpy = 12345;

        // Note: the Japanese locale renders the full-width yen sign (￥).
        $this->assertInstanceOf(Money::class, $product->jpy);
        $this->assertSame('12345', (string) $product->jpy);
        $this->assertSame('￥12,345', $product->jpy->currency());

        $product->jpy = '￥12,345';

        $this->assertSame(12345, $product->getAttributes()['jpy']);
        $this->assertSame('12345', (string) $product->jpy);
        $this->assertSame('￥12,345', $product->jpy->currency());
    }

    public function test_money_value_object_is_immutable_and_json_serializes_raw(): void
    {
        $product = new Product;

        $product->price = 12346;

        $money = $product->price;

        $this->assertSame(12346, $money->value());
        $this->assertSame(12346, $money->jsonSerialize());
        $this->assertSame('{"price":12346}', $product->toJson());
    }

    public function test_null_values_are_preserved(): void
    {
        $product = new Product;

        $this->assertNull($product->price);

        $product->price = null;

        $this->assertArrayHasKey('price', $product->getAttributes());
        $this->assertNull($product->getAttributes()['price']);
        $this->assertNull($product->price);
    }

    public function test_invalid_values_read_as_null(): void
    {
        $product = new Product;
        $product->setRawAttributes([
            'price' => 'not a number',
            'discount' => 'not a number',
        ]);

        $this->assertNull($product->price);
        $this->assertNull($product->discount);
    }

    public function test_config_defaults_are_applied_by_service_provider(): void
    {
        $this->assertSame('ko_KR', config('n-format.locale'));
        $this->assertSame('KRW', config('n-format.currency'));
        $this->assertSame('ko_KR', NFormat::$locale);
        $this->assertSame('KRW', NFormat::$currency);
    }

    public function test_save_and_fresh_roundtrip(): void
    {
        $product = Product::create([
            'price' => 12346,
            'jpy' => 12345,
        ]);

        $this->assertSame('12346', (string) $product->price);
        $this->assertSame(12346, $product->getRawOriginal('price'));

        $fresh = $product->fresh();

        $this->assertInstanceOf(Money::class, $fresh->price);
        $this->assertSame('12346', (string) $fresh->price);
        $this->assertSame('12300', $fresh->price->price(-2));
        $this->assertSame('12300', $fresh->price->smartPrice());
        $this->assertSame('12345', (string) $fresh->jpy);

        $this->assertSame(1, Product::where('price', 12346)->count());
        $this->assertSame(1, Product::where('jpy', 12345)->count());
    }

    public function test_as_currency_class_is_configurable_via_constructor(): void
    {
        $cast = new AsCurrency('ja_JP', 'JPY');

        $product = new Product;
        $product->jpy = 1000;

        $this->assertSame('1000', (string) $product->jpy);
        $this->assertSame('￥1,000', $product->jpy->currency());
    }

    public function test_as_number_stores_raw_and_returns_number_object(): void
    {
        $product = new Product;

        $product->discount = 12346;

        $this->assertSame(12346, $product->getAttributes()['discount']);
        $this->assertInstanceOf(Number::class, $product->discount);
        $this->assertSame('12,346', (string) $product->discount);
        $this->assertSame('12,346', $product->discount->decimal());
    }

    public function test_number_object_exposes_nformat_helpers(): void
    {
        $product = new Product;

        $product->discount = 12346;

        $this->assertSame('1,234,600%', $product->discount->percent());
        $this->assertSame('12,346%', $product->discount->rawPercent());
        $this->assertSame(NFormat::spellOut(12346), $product->discount->spellOut());
    }

    public function test_number_percentage_helpers_preserve_floats(): void
    {
        $product = new Product;

        $product->discount = 12.5;

        $this->assertSame('1,250%', $product->discount->percent());
        $this->assertSame('12.5%', $product->discount->rawPercent());
    }

    public function test_number_ordinal_spell_out(): void
    {
        $product = new Product;

        $product->rank = 10;

        $this->assertSame('열번째', $product->rank->ordinalSpellOut());
        $this->assertSame(NFormat::ordinalSpellOut(10), $product->rank->ordinalSpellOut());
    }

    public function test_number_accepts_formatted_string_and_number_object(): void
    {
        $product = new Product;

        // rawPercent() output parses back to the original number.
        $product->discount = '12,346%';

        $this->assertSame(12346, $product->getAttributes()['discount']);

        $number = $product->discount;
        $product->count = $number;

        $this->assertSame(12346, $product->getAttributes()['count']);
    }

    public function test_number_value_object_is_immutable_and_json_serializes_raw(): void
    {
        $product = new Product;

        $product->count = 12346;

        $number = $product->count;

        $this->assertSame(12346, $number->value());
        $this->assertSame(12346, $number->jsonSerialize());
        $this->assertSame('{"count":12346}', $product->toJson());
    }

    public function test_number_null_values_are_preserved(): void
    {
        $product = new Product;

        $this->assertNull($product->count);

        $product->count = null;

        $this->assertArrayHasKey('count', $product->getAttributes());
        $this->assertNull($product->getAttributes()['count']);
        $this->assertNull($product->count);
    }

    public function test_number_save_and_fresh_roundtrip(): void
    {
        $product = Product::create([
            'price' => 12346,
            'jpy' => 12345,
            'discount' => 12346,
            'count' => 12346,
            'rank' => 10,
        ]);

        $fresh = $product->fresh();

        $this->assertInstanceOf(Number::class, $fresh->discount);
        $this->assertSame('1,234,600%', $fresh->discount->percent());
        $this->assertSame('12,346%', $fresh->discount->rawPercent());
        $this->assertSame('열번째', $fresh->rank->ordinalSpellOut());

        $this->assertSame(1, Product::where('discount', 12346)->count());
        $this->assertSame(1, Product::where('rank', 10)->count());
    }
}
