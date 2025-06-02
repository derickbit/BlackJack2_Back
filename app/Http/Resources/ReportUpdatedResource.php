<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ReportUpdatedResource extends ReportResource
{
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(200, 'Report Atualizado!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Report atualizado com sucesso!',
        ];
    }
}
