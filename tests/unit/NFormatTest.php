<?php

declare(strict_types=1);

use Cable8mm\NFormat\NFormat;
use PHPUnit\Framework\TestCase;

class NFormatTest extends TestCase
{
    public function testSpellOut()
    {
        $spellOut = NFormat::spellOut(5);
        $this->assertEquals($spellOut, '오');
    }

    public function testSpellOutJapanese()
    {
        NFormat::$locale = 'ja_JP';

        $spellOut = NFormat::spellOut(5);
        $this->assertEquals($spellOut, '五');
    }

    public function testOrdinal()
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
}
