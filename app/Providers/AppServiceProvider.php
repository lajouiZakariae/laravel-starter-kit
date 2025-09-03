<?php

namespace App\Providers;

use App\Context\UserContext;
use App\Interfaces\UserContextInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        app()->bind(UserContextInterface::class, UserContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
