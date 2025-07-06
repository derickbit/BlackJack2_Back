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
            'jogador' => [
                'id' => $this->jogador,
                'nome' => $this->jogador1 ? $this->jogador1->name : null,
            ],
            'jogo' => $this->jogo,
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
