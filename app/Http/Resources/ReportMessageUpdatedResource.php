<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ReportMessageUpdatedResource extends ReportMessageResource
{
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(200, 'Mensagem Atualizada!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Mensagem atualizada com sucesso!',
        ];
    }
}
