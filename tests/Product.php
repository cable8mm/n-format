<?php

declare(strict_types=1);

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\Casts\AsCurrency;
use Illuminate\Database\Eloquent\Model;

/**
 * A test model using the AsCurrency cast.
 */
class Product extends Model
{
    public $timestamps = false;

    protected $table = 'products';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => AsCurrency::class,
            'jpy' => AsCurrency::class.':ja_JP,JPY',
        ];
    }
}
