<?php

declare(strict_types=1);

namespace Cable8mm\NFormat;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Laravel integration.
 *
 * Merges the package config and registers the auto-discovered service provider
 * so that NFormat::$locale and NFormat::$currency follow the app configuration.
 */
class NFormatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/n-format.php', 'n-format');
    }

    public function boot(): void
    {
        NFormat::$locale = (string) config('n-format.locale');
        NFormat::$currency = (string) config('n-format.currency');

        $this->publishes([
            __DIR__.'/../config/n-format.php' => config_path('n-format.php'),
        ], 'n-format');
    }
}
