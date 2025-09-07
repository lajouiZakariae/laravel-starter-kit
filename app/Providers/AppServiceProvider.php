<?php

namespace App\Providers;

use App\Context\UserContext;
use App\Interfaces\UserContextInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    public $bindings = [
        UserContextInterface::class => UserContext::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
