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
        Watcher::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'user_id' => $request->user()->id,
            'query_type' => $request->input('query_type'),
            'query'  => $request->input('query'),
        ]);

        return new JsonResponse([
            'status' => 'successful',
            'message' => 'successfully created user'
        ]);
    }
}
