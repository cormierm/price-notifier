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
            'query'  => $request->input('query'),
            'interval_id' => $request->input('interval_id'),
            'alert_value' => $request->input('alert_value'),
            'client' => $request->input('client'),
            'xpath_stock' => $request->input('xpath_stock'),
            'stock_text' => $request->input('stock_text'),
            'stock_alert' => $request->input('stock_alert'),
            'stock_contains' => $request->input('stock_contains'),
        ]);

        event(new WatcherCreatedOrUpdated(WatcherResource::make($watcher)));

        $request->user()->templates()->updateOrCreate(
            [
                'domain' => $watcher->urlDomain(),
                'user_id' => $request->user()->id
            ],
            [
                'xpath_value' => $request->input('query'),
                'client' =>  $request->input('client'),
                'xpath_stock' => $request->input('xpath_stock'),
                'stock_text' => $request->input('stock_text'),
                'stock_contains' => $request->input('stock_contains'),
            ]
        );

        return new JsonResponse([
            'message' => 'Successfully created watcher',
            'watcher' => $watcher
        ]);
    }
}
