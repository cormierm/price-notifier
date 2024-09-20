<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\UpdateRequest;
use App\Http\Resources\WatcherResource;
use App\Models\Watcher;
use Illuminate\Http\JsonResponse;

class Update extends Controller
{
    public function __invoke(UpdateRequest $request, Watcher $watcher): JsonResponse
    {
        $watcher->update($request->validated());

        if ($request->boolean('update_queries')) {
            $request->user()->templates()->updateOrCreate(
                [
                    'domain' => $watcher->urlDomain(),
                    'user_id' => $request->user()->id
                ],
                [
                    'price_query' => $watcher->price_query,
                    'price_query_type' => $watcher->price_query_type,
                    'stock_query' => $watcher->stock_query,
                    'stock_query_type' => $watcher->stock_query_type,
                    'client' => $watcher->client,
                    'stock_text' => $watcher->stock_text,
                    'stock_condition' => $watcher->stock_condition,
                    'stock_requires_price' => $watcher->stock_requires_price,
                ]
            );
        }

        return new JsonResponse([
            'message' => 'Successfully updated',
            'watcher' => WatcherResource::make($watcher)
        ]);
    }
}
