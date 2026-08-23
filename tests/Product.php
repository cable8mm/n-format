<?php

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\Casts\CurrencyCast;
use Cable8mm\NFormat\Casts\DecimalCast;
use Cable8mm\NFormat\Casts\OrdinalCast;
use Cable8mm\NFormat\Casts\PercentCast;
use Cable8mm\NFormat\Casts\PriceCast;
use Cable8mm\NFormat\Casts\RawPercentCast;
use Cable8mm\NFormat\Casts\SmartPriceCast;
use Cable8mm\NFormat\Casts\SpellOutCast;
use Illuminate\Database\Eloquent\Model;

/**
 * A test model that uses every NFormat eloquent cast.
 */
class Product extends Model
{
    public $timestamps = false;

    protected $table = 'products';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => CurrencyCast::class,
            'smart_price' => SmartPriceCast::class,
            'decimal_price' => DecimalCast::class,
            'rounded' => PriceCast::class.':-2',
            'discount' => PercentCast::class,
            'raw_discount' => RawPercentCast::class,
            'count' => SpellOutCast::class,
            'rank' => OrdinalCast::class,
            'jpy' => CurrencyCast::class.':ja_JP,JPY',
        ];
    }
}
