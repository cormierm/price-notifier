<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Resources\WatcherResource;
use App\Models\Interval;
use App\Models\Watcher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Show extends Controller
{
    public function __invoke(Request $request, Watcher $watcher)
    {

        return Inertia::render('Watcher/Show', [
            'watcher' => WatcherResource::make($watcher)->toArray($request),
            'priceChanges' => $watcher->priceChanges()->latest('created_at')->get()->toArray(),
            'stockChanges' => $watcher->stockChanges()->latest('created_at')->limit(10)->get()->toArray(),
            'intervals' => Interval::all()->toArray()
        ]);
    }
}
