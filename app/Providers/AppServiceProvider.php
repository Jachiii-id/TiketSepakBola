<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Blade::component('components.modal', 'modal');
        Blade::component('components.form', 'form');
        Blade::component('components.card', 'card');
        //
        // URL::forceScheme('https');
        // if (App::environment('production')) {
        // }
    }
}
