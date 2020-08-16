<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Interval;
use App\Watcher;

class Edit extends Controller
{
    public function __invoke(Watcher $watcher)
    {
        return view('watcher.edit', [
            'intervals' => Interval::all(),
            'watcher' => $watcher,
        ]);
    }
}