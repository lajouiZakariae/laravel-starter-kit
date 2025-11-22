<?php

namespace App\Providers;

use App\Contracts\UserContext;
use App\Support\Auth\UserContext as UserContextImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * @var array<string, string>
     */
    public array $bindings = [
        UserContext::class => UserContextImpl::class,
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
