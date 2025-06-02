<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return ReportResource::collection($this->collection)->toArray($request);
    }
}


