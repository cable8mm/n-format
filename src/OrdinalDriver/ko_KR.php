<?php

return function (int $number) {
    $ordinals = [
        1 => '첫',
        2 => '두',
        3 => '세',
        4 => '네',
        5 => '다섯',
        6 => '여섯',
        7 => '일곱',
        8 => '여덞',
        9 => '아홉',
        10 => '열',
    ];

    return array_key_exists($number, $ordinals) ?
        $ordinals[$number].'번째'
        : (new NumberFormatter('ko_KR', NumberFormatter::SPELLOUT))->format($number).'번째';
};
