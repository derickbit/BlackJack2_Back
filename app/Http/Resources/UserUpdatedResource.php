<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class UserUpdatedResource extends UserResource
{
   /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // The frontend replaces its authenticated account with this response.
        // Preserve id/role without ever exposing password hashes.
        return parent::toArray($request);
    }




    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(200, 'User Atualizada!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'User registrada com sucesso!!',
        ];
    }
}
