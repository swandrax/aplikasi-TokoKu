<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Models\ChatbotPrompt;

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
        Paginator::useBootstrapFour();

        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        \Illuminate\Support\Facades\Blade::component('layouts.pembeli', 'pembeli-layout');
        \Illuminate\Support\Facades\Blade::component('layouts.kasir', 'kasir-layout');
        \Illuminate\Support\Facades\Blade::component('layouts.admin', 'admin-layout');
        \Illuminate\Support\Facades\Blade::component('layouts.guest', 'guest-layout');
    }
}
