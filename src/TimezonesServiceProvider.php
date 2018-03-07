<?php

namespace RicoGoh\Timezones;

use Illuminate\Support\ServiceProvider;

class TimezonesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $pkg = 'ricogoh/timezones';

        $this->loadViewsFrom(__DIR__.'/views', $pkg);

        // php artisan vendor:publish --provider="RicoGoh\Timezones\TimezonesServiceProvider"
        // OR
        // php artisan vendor:publish --tag="ricogoh.timezones"
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/'.$pkg),
        ], 'ricogoh.timezones');
    }

    /**
     * Register services.
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('RicoGoh\\Timezones\\TimezonesController');
    }
}
