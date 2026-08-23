<?php

namespace Cable8mm\NFormat\Tests;

use Cable8mm\NFormat\NFormatServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        if (extension_loaded('pdo_sqlite')) {
            Schema::create('products', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('price')->nullable();
                $table->integer('smart_price')->nullable();
                $table->integer('decimal_price')->nullable();
                $table->integer('rounded')->nullable();
                $table->integer('discount')->nullable();
                $table->integer('raw_discount')->nullable();
                $table->integer('count')->nullable();
                $table->integer('rank')->nullable();
                $table->integer('jpy')->nullable();
            });
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            NFormatServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
