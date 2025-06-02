<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class PartidaUpdatedResource extends PartidaResource
{
   /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'codpartida' => $this->codpartida,
            'jogador1_id' => [
                'id' => $this->jogador1_id,
                'nome' => $this->jogador1->name ?? null, // Nome do jogador 1
            ],
            'jogador2_id' => [
                'id' => $this->jogador2_id,
                'nome' => $this->jogador2->name ?? null, // Nome do jogador 2
            ],
            'vencedor_id' => [
                'id' => $this->vencedor_id,
                'nome' => $this->vencedor->name ?? null, // Nome do Vencedor
            ],
            'pontuacao' => $this->pontuacao,

        ];
    }




    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode(200, 'Partida Atualizada!');
    }

    public function with(Request $request): array
    {
        return [
            'message' => 'Partida registrada com sucesso!!',
        ];
    }
}
