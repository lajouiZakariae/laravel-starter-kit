<?php

namespace App\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

trait ApiResponse {
    protected function successResponse(JsonResource|ResourceCollection $resource, array $additional = []): JsonResponse {
        return $resource
            ->additional($additional)
            ->toResponse(app(Request::class))
            ->setStatusCode(SymfonyResponse::HTTP_OK);
    }

    protected function createdResponse(JsonResource $resource, array $additional = []): JsonResponse {
        return $resource
            ->additional($additional)
            ->toResponse(app(Request::class))
            ->setStatusCode(SymfonyResponse::HTTP_CREATED);
    }

    protected function deletedResponse(): Response {
        return response()->noContent();
    }
}
