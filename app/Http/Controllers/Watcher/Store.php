<?php

namespace App\Http\Controllers\Watcher;

use App\Events\WatcherCreatedOrUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\StoreRequest;
use App\Http\Resources\WatcherResource;
use App\Watcher;
use Illuminate\Http\JsonResponse;

class Store extends Controller
{
    public function __invoke(StoreRequest $request): JsonResponse
    {
        $watcher = Watcher::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'user_id' => $request->user()->id,
            'price_query' => $request->input('price_query'),
            'price_query_type' => $request->input('price_query_type'),
            'stock_query' => $request->input('stock_query'),
            'stock_query_type' => $request->input('stock_query_type'),
            'interval_id' => $request->input('interval_id'),
            'alert_value' => $request->input('alert_value'),
            'client' => $request->input('client'),
            'stock_text' => $request->input('stock_text'),
            'stock_alert' => $request->input('stock_alert'),
            'stock_condition' => $request->input('stock_condition'),
            'stock_requires_price' => $request->input('stock_requires_price'),
            'region_id' => $request->input('region_id'),
        ]);

        event(new WatcherCreatedOrUpdated(WatcherResource::make($watcher)));

        if ($request->boolean('update_queries')) {
            $request->user()->templates()->updateOrCreate(
                [
                    'domain' => $watcher->urlDomain(),
                    'user_id' => $request->user()->id
                ],
                [
                    'price_query' => $request->input('price_query'),
                    'price_query_type' => $request->input('price_query_type'),
                    'stock_query' => $request->input('stock_query'),
                    'stock_query_type' => $request->input('stock_query_type'),
                    'stock_text' => $request->input('stock_text'),
                    'stock_condition' => $request->input('stock_condition'),
                    'stock_requires_price' => $request->input('stock_requires_price'),
                    'client' => $request->input('client'),
                ]
            );
        }

        return new JsonResponse([
            'message' => 'Successfully created watcher',
            'watcher' => $watcher
        ]);
    }
}
