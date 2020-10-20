<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardPriceChangeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'created_at' => $this->resource->created_at,
            'price' => $this->resource->price,
            'watcher' => [
                'id' => $this->resource->watcher->id,
                'name' => $this->resource->watcher->name,
                'url' => $this->resource->watcher->url,
                'url_domain' => $this->resource->watcher->urlDomain(),
            ],
        ];
    }
}
