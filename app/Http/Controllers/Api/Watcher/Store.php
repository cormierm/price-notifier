<?php

namespace App\Http\Controllers\Api\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\StoreRequest;
use Illuminate\Http\JsonResponse;

class Store extends Controller
{
    public function __invoke(StoreRequest $request): JsonResponse
    {
        $request->user()->watchers()->create([
            'user_id' => $request->user()->id,
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'price_query' => $request->input('price_query'),
            'price_query_type' => $request->input('price_query_type'),
            'stock_query' => $request->input('stock_query'),
            'stock_query_type' => $request->input('stock_query_type'),
            'stock_condition' => $request->input('stock_condition'),
            'stock_text' => $request->input('stock_text'),
            'client' => $request->input('client'),
            'stock_alert' => false,
            'interval_id' => $request->input('interval_id'),
        ]);

        return response()->json();
    }
}
