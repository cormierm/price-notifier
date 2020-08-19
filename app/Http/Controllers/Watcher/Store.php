<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\StoreRequest;
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
        ]);

        $request->user()->templates()->updateOrCreate(
            [
                'domain' => $watcher->urlDomain(),
                'user_id' => $request->user()->id
            ],
            [
                'xpath_value' => $request->input('query'),
                'xpath_name' => $request->input('xpath_name'),
                'client' =>  $request->input('client'),
            ]
        );

        return new JsonResponse([
            'message' => 'Successfully created watcher',
            'watcher' => $watcher
        ]);
    }
}
