<?php

namespace App\Http\Controllers\Api;

use App\Data\PaginationData;
use App\Data\SortingData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Country\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class CountryController extends Controller {
    /**
     * Display a listing of the countries.
     */
    public function index(Request $request, PaginationData $paginationData): AnonymousResourceCollection {
        $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'sort_by' => ['sometimes', 'string', 'in:common_name,iso_3166_1_alpha2'],
            'order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ]);

        if ($request->string('sort_by')->is('common_name')) {
            $request->merge(['sort_by' => 'common_name->en']);
        }

        $countriesQuery = Country::query();

        $countriesQuery->select(['id', 'iso_3166_1_alpha2', 'common_name']);

        $countriesQuery->when(
            $request->filled('search'),
            fn ($query) => $query->search($request->search)
        );

        $countriesQuery->with(['media']);

        $sortingData = SortingData::from($request);

        $countriesQuery->orderBy($sortingData->sortBy, $sortingData->order);

        $countries = $countriesQuery->paginate($paginationData->perPage);

        return CountryResource::collection($countries);
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country): CountryResource {
        return new CountryResource($country);
    }
}
