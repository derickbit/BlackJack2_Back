<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReportMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user_id,
                'nome' => $this->user->name ?? null,
            ],
            'mensagem' => $this->mensagem,
            'imagem' => $this->imagem ? url(Storage::url($this->imagem)) : null,
            'created_at' => $this->created_at,
        ];
    }


    public function with(Request $request): array
    {
        return [
            'meta' => [
                'status' => 'success',
            ],
        ];
    }
}
