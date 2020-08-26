<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Resources\WatcherResource;
use App\Interval;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __invoke(Request $request)
    {
        return view('watcher.index', [
            'watchers' => WatcherResource::collection($request->user()->watchers),
            'intervals' => Interval::all()
        ]);
    }
}
