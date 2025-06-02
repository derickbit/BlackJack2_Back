<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ReportStoredResource extends ReportResource
{
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(201, 'Report Criado!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Report registrado com sucesso!',
        ];
    }
}
