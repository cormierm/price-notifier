<?php

namespace App\Http\Controllers\Watcher;

use App\Events\WatcherDeleted;
use App\Http\Controllers\Controller;
use App\Watcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Destroy extends Controller
{
    public function __invoke(Request $request, Watcher $watcher): JsonResponse
    {
        $id = $watcher->id;

        $watcher->logs()->delete();
        $watcher->priceChanges()->delete();
        $watcher->delete();

        event(new WatcherDeleted($id, $request->user()->id));

        return new JsonResponse([
            'message' => 'Successfully deleted watcher',
        ]);
    }
}
