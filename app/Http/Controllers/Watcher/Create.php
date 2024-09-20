<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Models\Interval;
use App\Models\Region;

class Create extends Controller
{
    public function __invoke()
    {
        return view('watcher.create', [
            'intervals' => Interval::all(),
            'regions' => Region::all(),
        ]);
    }
}
