<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (getenv('OTEL_ENABLED') === 'true') {
            require_once __DIR__ . '/../../observability/otel-config.php';
            setupOpenTelemetry();
        }
    }
}
