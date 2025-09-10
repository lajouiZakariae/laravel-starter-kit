<?php

use App\Exceptions\Handlers\NotFoundExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api/api.php',
        health: '/up',
        then: function (): void {
            Route::middleware(['api'])
                ->prefix('api/auth')
                ->name('api.auth.')
                ->group(base_path('routes/api/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(
            fn (NotFoundHttpException $e): JsonResponse|Response => app(NotFoundExceptionHandler::class)->handle($e)
        );
    })
    ->create();
