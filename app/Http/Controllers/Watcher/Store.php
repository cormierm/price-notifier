<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Watcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Store extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $watcher = Watcher::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'user_id' => $request->user()->id,
            'query'  => $request->input('query'),
            'interval_id' => $request->input('interval_id'),
        ]);

        return new JsonResponse([
            'message' => 'Successfully created watcher',
            'watcher' => $watcher
        ]);
    }
}
