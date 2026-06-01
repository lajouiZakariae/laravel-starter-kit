<?php

namespace App\Contracts\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

interface ApiResponse {
    public function successResponse(JsonResource|ResourceCollection|array $resource, array $additional = []): JsonResponse;

    public function createdResponse(JsonResource|array $resource, array $additional = []): JsonResponse;

    public function notFoundResponse(array $payload = []): JsonResponse;

    public function deletedResponse(): Response;

    public function emptyResponse(): Response;

    public function acceptedResponse(array $additional = []): JsonResponse;
}
