<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ReportMessageStoredResource extends ReportMessageResource
{
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(201, 'Mensagem Criada!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Mensagem registrada com sucesso!',
        ];
    }
}
