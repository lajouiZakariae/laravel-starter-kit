<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * @var \App\Models\User
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $userInstance = $this->resource;

        return [
            'id' => $userInstance->id,
            'first_name' => $userInstance->first_name,
            'last_name' => $userInstance->last_name,
            'email' => $userInstance->email,
            'email_verified_at' => $userInstance->email_verified_at,
            'created_at' => $userInstance->created_at,
            'updated_at' => $userInstance->updated_at,
        ];
    }
}
