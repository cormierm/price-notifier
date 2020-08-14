<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Watcher;
use Illuminate\Http\JsonResponse;

class Destroy extends Controller
{
    public function __invoke(Watcher $watcher): JsonResponse
    {
        $watcher->delete();

        return new JsonResponse([
            'message' => 'Successfully deleted watcher',
        ]);
    }
}
