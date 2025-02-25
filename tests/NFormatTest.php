<?php

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\NFormat;
use PHPUnit\Framework\TestCase;

class NFormatTest extends TestCase
{
    public function test_spell_out(): void
    {
        $spellOut = NFormat::spellOut(5);
        $this->assertEquals($spellOut, '오');
    }

    public function test_spell_out_japanese(): void
    {
        NFormat::$locale = 'ja_JP';

        $spellOut = NFormat::spellOut(5);
        $this->assertEquals($spellOut, '五');
    }

    public function test_ordinal(): void
    {
        NFormat::$locale = 'ko_KR';

        $ordinal = NFormat::ordinalSpellOut(1);
        $this->assertEquals('첫번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(2);
        $this->assertEquals('두번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(3);
        $this->assertEquals('세번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(4);
        $this->assertEquals('네번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(10);
        $this->assertEquals('열번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(20);
        $this->assertEquals('이십번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(21);
        $this->assertEquals('이십일번째', $ordinal);
        $ordinal = NFormat::ordinalSpellOut(149);
        $this->assertEquals('백사십구번째', $ordinal);
    }

    public function test_currency(): void
    {
        NFormat::$locale = 'ko_KR';

        $currency = NFormat::currency(12346);
        $this->assertEquals('₩12,346', $currency);
    }

    public function test_currency_zero(): void
    {
        NFormat::$locale = 'ko_KR';

        $currency = NFormat::currency(0, '-');
        $this->assertEquals('-', $currency);

        $currency = NFormat::currency(123, '-');
        $this->assertEquals('₩123', $currency);

        $currency = NFormat::currency(null, '-');
        $this->assertEquals('-', $currency);
    }

    public function test_currency_speech_out(): void
    {
        NFormat::$locale = 'ko_KR';

        $currency = NFormat::currencySpellOut(12346);
        $this->assertEquals('12,346 원', $currency);
    }

    public function test_percent(): void
    {
        NFormat::$locale = 'ko_KR';

        $percent = NFormat::percent(12346);

        $this->assertEquals('1,234,600%', $percent);
    }

    public function test_raw_percent(): void
    {
        NFormat::$locale = 'ko_KR';

        $percent = NFormat::rawPercent(12346);

        $this->assertEquals('12,346%', $percent);
    }

    public function test_decimal(): void
    {
        NFormat::$locale = 'ko_KR';

        $decimal = NFormat::decimal(12346);
        $this->assertEquals('12,346', $decimal);

        $decimal = NFormat::decimal(123, '-');
        $this->assertEquals('123', $decimal);

        $decimal = NFormat::decimal(null, '-');
        $this->assertEquals('-', $decimal);
    }

    public function test_price(): void
    {
        NFormat::$locale = 'ko_KR';

        $price = NFormat::price(12346, -2);
        $this->assertEquals('12300', $price);

        $price = NFormat::price(12346, 2);
        $this->assertEquals('12346', $price);

        $price = NFormat::price(12346.0);
        $this->assertEquals('12346.00', $price);

        $price = NFormat::price(12346.23123, -2);
        $this->assertEquals('12300.00', $price);

        $price = NFormat::price(12346.23, 1);
        $this->assertEquals('12346.20', $price);
    }

    public function test_smart_price(): void
    {
        NFormat::$locale = 'ko_KR';

        $price = NFormat::smartPrice(12346);
        $this->assertEquals('12300', $price);

        $price = NFormat::smartPrice(123467);
        $this->assertEquals('123000', $price);

        $price = NFormat::smartPrice(1234678);
        $this->assertEquals('1230000', $price);

        $price = NFormat::smartPrice(12346432);
        $this->assertEquals('12350000', $price);

        $price = NFormat::smartPrice(3212343232);
        $this->assertEquals('3212340000', $price);
    }
}
