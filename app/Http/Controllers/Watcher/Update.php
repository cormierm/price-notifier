<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Watcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Update extends Controller
{
    public function __invoke(Request $request, Watcher $watcher): JsonResponse
    {
        $watcher->update([
            'name' => $request->input('name'),
            'query' => $request->input('query'),
            'url' => $request->input('url'),
        ]);

        return new JsonResponse([
            'message' => 'Successfully updated',
            'watcher' => $watcher,
        ]);
    }
}
