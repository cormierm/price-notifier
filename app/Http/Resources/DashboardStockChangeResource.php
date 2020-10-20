<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStockChangeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'created_at' => $this->resource->created_at,
            'stock' => $this->resource->stock ? 'Yes' : 'No',
            'watcher' => [
                'id' => $this->resource->watcher->id,
                'name' => $this->resource->watcher->name,
                'url' => $this->resource->watcher->url,
                'url_domain' => $this->resource->watcher->urlDomain(),
            ],
        ];
    }
}
