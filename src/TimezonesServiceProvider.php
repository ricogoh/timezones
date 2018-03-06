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
        $this->loadViewsFrom(__DIR__.'/views', 'timezones');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/ricogoh/timezones'),
        ]);
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
