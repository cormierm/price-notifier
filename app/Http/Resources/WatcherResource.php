<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WatcherResource extends JsonResource
{
    public function toArray($request): array
    {
        return array_merge($this->resource->load(['interval', 'region'])->toArray(), [
            'url_domain' => $this->resource->urlDomain(),
            'status' => $this->status
        ]);
    }
}
