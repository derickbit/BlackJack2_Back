<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class PartidaStoredResource extends PartidaResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
 
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(201, 'Partida Criada!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Partida registrada com sucesso!!',
        ];
    }
}
