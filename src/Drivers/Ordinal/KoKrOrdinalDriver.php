<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Drivers\Ordinal;

use Cable8mm\NFormat\Drivers\Contracts\OrdinalDriver;
use NumberFormatter;

/**
 * Korean ordinal driver (ko_KR).
 */
final class KoKrOrdinalDriver implements OrdinalDriver
{
    /**
     * Unique ordinal words for the numbers 1 to 10.
     *
     * @var array<int, string>
     */
    private const ORDINALS = [
        1 => '첫',
        2 => '두',
        3 => '세',
        4 => '네',
        5 => '다섯',
        6 => '여섯',
        7 => '일곱',
        8 => '여덟',
        9 => '아홉',
        10 => '열',
    ];

    public function spellOut(int $number): string
    {
        if (array_key_exists($number, self::ORDINALS)) {
            return self::ORDINALS[$number].'번째';
        }

        return (new NumberFormatter('ko_KR', NumberFormatter::SPELLOUT))->format($number).'번째';
    }
}
