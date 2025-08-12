<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Filament\Widgets\StatsOverview;
use Filament\Facades\Filament;

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


    public function boot()
    {
        Filament::serving(function () {
            Filament::registerWidgets([
                StatsOverview::class,
            ]);
        });
    }
}
