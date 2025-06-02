<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class UserStoredResource extends UserResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(201, 'Usuário Criada!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Usuário registrada com sucesso!!',
        ];
    }
}
