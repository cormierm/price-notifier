<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Models\Interval;
use App\Models\Region;
use App\Models\Watcher;

class Edit extends Controller
{
    public function __invoke(Watcher $watcher)
    {
        return view('watcher.edit', [
            'intervals' => Interval::all(),
            'regions' => Region::all(),
            'watcher' => $watcher,
        ]);
    }
}
