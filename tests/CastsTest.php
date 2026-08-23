<?php

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\NFormat;

class CastsTest extends TestCase
{
    public function test_currency_cast_formats_and_stores_raw_number(): void
    {
        $product = new Product;

        $product->price = 12346;

        $this->assertSame(12346, $product->getAttributes()['price']);
        $this->assertSame('₩12,346', $product->price);
    }

    public function test_currency_cast_accepts_formatted_string(): void
    {
        $product = new Product;

        $product->price = '₩12,350원';

        $this->assertSame(12350, $product->getAttributes()['price']);
        $this->assertSame('₩12,350', $product->price);
    }

    public function test_currency_cast_with_locale_and_currency(): void
    {
        $product = new Product;

        $product->jpy = 12345;

        // Note: the Japanese locale renders the full-width yen sign (￥).
        $this->assertSame('￥12,345', $product->jpy);

        $product->jpy = '￥12,345';

        $this->assertSame(12345, $product->getAttributes()['jpy']);
        $this->assertSame('￥12,345', $product->jpy);
    }

    public function test_price_cast_rounds_the_number(): void
    {
        $product = new Product;

        $product->rounded = 12346;

        $this->assertSame('12300', $product->rounded);
        $this->assertSame(12346, $product->getAttributes()['rounded']);
    }

    public function test_smart_price_cast(): void
    {
        $product = new Product;

        $product->smart_price = 1234678;

        $this->assertSame('1230000', $product->smart_price);
        $this->assertSame(1234678, $product->getAttributes()['smart_price']);
    }

    public function test_decimal_cast(): void
    {
        $product = new Product;

        $product->decimal_price = 12346;

        $this->assertSame('12,346', $product->decimal_price);
    }

    public function test_percent_cast(): void
    {
        $product = new Product;

        $product->discount = 10;

        $this->assertSame('1,000%', $product->discount);
    }

    public function test_raw_percent_cast(): void
    {
        $product = new Product;

        $product->raw_discount = 12346;

        $this->assertSame('12,346%', $product->raw_discount);
    }

    public function test_spell_out_cast(): void
    {
        $product = new Product;

        $product->count = 5;

        $this->assertSame('오', $product->count);
    }

    public function test_ordinal_cast(): void
    {
        $product = new Product;

        $product->rank = 10;

        $this->assertSame('열번째', $product->rank);
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
            'smart_price' => 1234678,
            'decimal_price' => 12346,
            'rounded' => 12346,
            'discount' => 10,
            'raw_discount' => 12346,
            'count' => 5,
            'rank' => 10,
            'jpy' => 12345,
        ]);

        $this->assertSame('₩12,346', $product->price);
        $this->assertSame(12346, $product->getRawOriginal('price'));

        $fresh = $product->fresh();

        $this->assertSame('₩12,346', $fresh->price);
        $this->assertSame('1230000', $fresh->smart_price);
        $this->assertSame('12,346', $fresh->decimal_price);
        $this->assertSame('12300', $fresh->rounded);
        $this->assertSame('1,000%', $fresh->discount);
        $this->assertSame('12,346%', $fresh->raw_discount);
        $this->assertSame('오', $fresh->count);
        $this->assertSame('열번째', $fresh->rank);
        $this->assertSame('￥12,345', $fresh->jpy);

        $this->assertSame(1, Product::where('price', 12346)->count());
        $this->assertSame(1, Product::where('jpy', 12345)->count());
    }
}
