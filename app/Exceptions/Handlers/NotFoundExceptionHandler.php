<?php

namespace App\Exceptions\Handlers;

use App\Concerns\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundExceptionHandler {
    use ApiResponse;

    public function handle(NotFoundHttpException $exception): JsonResponse|Response {
        $previousException = $exception->getPrevious();

        if ($previousException instanceof ModelNotFoundException) {
            $modelName = $previousException->getModel();

            $titleCase = str(class_basename($modelName))->snake()->replace('_', ' ')->title();

            return $this->notFoundResponse(['message' => "$titleCase not found"]);
        }

        return $this->notFoundResponse(['message' => 'Route not found']);
    }
}
