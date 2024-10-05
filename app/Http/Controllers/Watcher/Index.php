<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Resources\WatcherResource;
use App\Models\Interval;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Index extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Watcher/Index', [
            'watchers' => WatcherResource::collection($request->user()->watchers)->toArray($request),
            'intervals' => Interval::all()->toArray(),
            'userId' => $request->user()->id,
        ]);
    }
}
