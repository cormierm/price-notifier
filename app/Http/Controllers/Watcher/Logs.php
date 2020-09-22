<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Watcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Logs extends Controller
{
    public function __invoke(Request $request, Watcher $watcher): JsonResponse
    {
        return new JsonResponse(
            $watcher->logs()
                ->orderByDesc('created_at')
                ->limit($request->input('limit', 5))
                ->get()
                ->toArray()
        );
    }
}
