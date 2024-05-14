<?php

namespace App\Providers;

use App\Models\GuestHouse;
use App\Observers\GuesthouseObserver;
use Illuminate\Support\ServiceProvider;

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
        GuestHouse::observe(GuesthouseObserver::class);
    }
}
