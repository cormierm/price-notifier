<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateWatcher;
use App\Utils\HtmlFetcher;
use App\Watcher;
use Illuminate\Http\JsonResponse;

class Sync extends Controller
{
    public function __invoke(Watcher $watcher, HtmlFetcher $fetcher)
    {
        UpdateWatcher::dispatch($watcher);

        return new JsonResponse([
            'watcher' => $watcher->fresh()
        ]);
    }
}
