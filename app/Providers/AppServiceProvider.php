<?php

namespace App\Providers;

use App\Support\UserContext;
use App\Contracts\UserContextInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * @var array<string, string>
     */
    public array $bindings = [
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
