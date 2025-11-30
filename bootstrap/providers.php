<?php

use App\Providers\AppServiceProvider;
use App\Providers\OtpCacheServiceProvider;
use App\Providers\RateLimiterServiceProvider;

return [
    AppServiceProvider::class,
    RateLimiterServiceProvider::class,
    OtpCacheServiceProvider::class,
];
