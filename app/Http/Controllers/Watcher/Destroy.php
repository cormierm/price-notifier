<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Models\Watcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Destroy extends Controller
{
    public function __invoke(Request $request, Watcher $watcher): JsonResponse
    {
        $watcher->logs()->delete();
        $watcher->priceChanges()->delete();
        $watcher->stockChanges()->delete();
        $watcher->delete();

        return new JsonResponse([
            'message' => 'Successfully deleted watcher',
        ]);
    }
}
