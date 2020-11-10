<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Resources\WatcherResource;
use App\Jobs\UpdateWatcher;
use App\Watcher;
use Illuminate\Http\JsonResponse;

class Sync extends Controller
{
    public function __invoke(Watcher $watcher)
    {
        UpdateWatcher::dispatch($watcher);

        return new JsonResponse([
            'watcher' => WatcherResource::make($watcher->fresh())
        ]);
    }
}
