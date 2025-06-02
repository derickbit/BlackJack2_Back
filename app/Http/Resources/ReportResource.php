<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DenunciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        ...parent::toArray($request),
        'imagem'=>$this->when($this->imagem, env('APP_URL') .  Storage::url('denuncias/' . $this->imagem))
        ];
    }
}
