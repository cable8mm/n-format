<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\Casts\AsCurrency;
use Cable8mm\NFormat\NFormat;
use Cable8mm\NFormat\ValueObjects\Money;

class CastsTest extends TestCase
{
    public function test_cast_stores_raw_number_and_returns_money(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame(12346, $product->getAttributes()['price']);
        $this->assertInstanceOf(Money::class, $product->price);
        $this->assertSame('₩12,346', (string) $product->price);
        $this->assertSame('₩12,346', $product->price->currency());
    }

    public function test_currency_is_default_get_behavior(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame('₩12,346', (string) $product->price);
        $this->assertSame('₩12,346', $product->price->currency());
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
        $this->assertSame('₩12,350', (string) $product->price);
    }

    public function test_cast_with_locale_and_currency(): void
    {
        $product = new Product;

        $product->jpy = 12345;

        // Note: the Japanese locale renders the full-width yen sign (￥).
        $this->assertInstanceOf(Money::class, $product->jpy);
        $this->assertSame('￥12,345', (string) $product->jpy);

        $product->jpy = '￥12,345';

        $this->assertSame(12345, $product->getAttributes()['jpy']);
        $this->assertSame('￥12,345', (string) $product->jpy);
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

        $this->assertSame('₩12,346', (string) $product->price);
        $this->assertSame(12346, $product->getRawOriginal('price'));

        $fresh = $product->fresh();

        $this->assertInstanceOf(Money::class, $fresh->price);
        $this->assertSame('₩12,346', (string) $fresh->price);
        $this->assertSame('12300', $fresh->price->price(-2));
        $this->assertSame('12300', $fresh->price->smartPrice());
        $this->assertSame('￥12,345', (string) $fresh->jpy);

        $this->assertSame(1, Product::where('price', 12346)->count());
        $this->assertSame(1, Product::where('jpy', 12345)->count());
    }

    public function test_as_currency_class_is_configurable_via_constructor(): void
    {
        $cast = new AsCurrency('ja_JP', 'JPY');

        $product = new Product;
        $product->jpy = 1000;

        $this->assertSame('￥1,000', (string) $product->jpy);
    }
}
