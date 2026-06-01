<?php

namespace App\Support\Http;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiResponse implements \App\Contracts\Http\ApiResponse {
    public function __construct(
        private readonly Application $application,
        private readonly ResponseFactory $responseFactory,
    ) {}

    public function successResponse(JsonResource|ResourceCollection|array $resource, array $additional = []): JsonResponse {
        if (is_array($resource)) {
            return $this->responseFactory->json($resource, SymfonyResponse::HTTP_OK);
        }

        return $resource
            ->additional($additional)
            ->toResponse($this->application->make(Request::class))
            ->setStatusCode(SymfonyResponse::HTTP_OK);
    }

    public function createdResponse(JsonResource|array $resource, array $additional = []): JsonResponse {
        if (is_array($resource)) {
            return $this->responseFactory->json($resource, SymfonyResponse::HTTP_CREATED);
        }

        return $resource
            ->additional($additional)
            ->toResponse($this->application->make(Request::class))
            ->setStatusCode(SymfonyResponse::HTTP_CREATED);
    }

    public function notFoundResponse(array $payload = []): JsonResponse {
        return $this->responseFactory->json($payload, SymfonyResponse::HTTP_NOT_FOUND);
    }

    public function deletedResponse(): Response {
        return $this->responseFactory->noContent();
    }

    public function emptyResponse(): Response {
        return $this->responseFactory->noContent();
    }

    public function acceptedResponse(array $additional = []): JsonResponse {
        return $this->responseFactory->json($additional, SymfonyResponse::HTTP_ACCEPTED);
    }
}
