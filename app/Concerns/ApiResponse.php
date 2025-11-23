<?php

namespace App\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait ApiResponse {
    protected function successResponse(JsonResource|ResourceCollection|array $resource, array $additional = []): JsonResponse {
        if (is_array($resource)) {
            return response()->json($resource);
        }

        return $resource
            ->additional($additional)
            ->toResponse(App::make(Request::class))
            ->setStatusCode(SymfonyResponse::HTTP_OK);
    }

    protected function notFoundResponse(array $payload = []): JsonResponse {
        return response()->json($payload, SymfonyResponse::HTTP_NOT_FOUND);
    }

    protected function createdResponse(JsonResource $resource, array $additional = []): JsonResponse {
        return $resource
            ->additional($additional)
            ->toResponse(App::make(Request::class))
            ->setStatusCode(SymfonyResponse::HTTP_CREATED);
    }

    protected function deletedResponse(): Response {
        return response()->noContent();
    }
}
