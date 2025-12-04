<?php

namespace App\Http\Controllers\Api;

use App\Data\PaginationData;
use App\Data\SortingData;
use App\Http\Controllers\Controller;
use App\Http\Resources\City\CityResource;
use App\Http\Responses\ApiResponse;
use App\Models\City;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller {
    public function __construct(
        private readonly ApiResponse $apiResponse
    ) {}

    /**
     * Display a listing of the cities.
     */
    public function index(Request $request, PaginationData $paginationData): JsonResponse {
        $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'sort_by' => ['sometimes', 'string', Rule::in(['name', 'created_at'])],
            'order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
        ]);

        $citiesQuery = City::query();

        $citiesQuery->select(['id', 'country_id', 'name', 'created_at', 'updated_at']);

        $citiesQuery->when(
            $request->filled('search'),
            fn ($query) => $query->whereLike('name', "%{$request->search}%")
        );

        $citiesQuery->when(
            $request->filled('country_id'),
            fn ($query) => $query->where('country_id', $request->country_id)
        );

        $citiesQuery->with('country');

        $sortingData = SortingData::from($request);

        $citiesQuery->orderBy($sortingData->sortBy, $sortingData->order);

        $cities = $citiesQuery->paginate($paginationData->perPage);

        return $this->apiResponse->successResponse(CityResource::collection($cities));
    }

    /**
     * Display the specified city.
     */
    public function show(string $id): JsonResponse {
        $city = City::query()
            ->with('country', fn (Relation $query) => $query->select(['id', 'iso_3166_1_alpha2', 'common_name']))
            ->findOrFail($id, ['id', 'country_id', 'name']);

        return $this->apiResponse->successResponse(new CityResource($city));
    }
}
