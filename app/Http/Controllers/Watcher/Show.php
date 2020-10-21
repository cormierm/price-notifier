<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Resources\WatcherResource;
use App\Interval;
use App\Watcher;

class Show extends Controller
{
    public function __invoke(Watcher $watcher)
    {

        return view('watcher.show', [
            'watcher' => WatcherResource::make($watcher),
            'priceChanges' => $watcher->priceChanges()->latest('created_at')->limit(10)->get(),
            'stockChanges' => $watcher->stockChanges()->latest('created_at')->limit(10)->get(),
            'intervals' => Interval::all()
        ]);
    }
}
