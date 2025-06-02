<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportMessageCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return ReportMessageResource::collection($this->collection)->toArray($request);
    }
}
