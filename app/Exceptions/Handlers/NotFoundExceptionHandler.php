<?php

namespace App\Exceptions\Handlers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundExceptionHandler {
    public function handle(NotFoundHttpException $exception): JsonResponse|Response {
        if ($exception->getPrevious() instanceof ModelNotFoundException) {
            return response()->noContent(SymfonyResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Route not found',
        ], SymfonyResponse::HTTP_NOT_FOUND);
    }
}
