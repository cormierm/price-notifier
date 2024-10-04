<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Models\Interval;
use App\Models\Region;
use App\Models\Watcher;
use Inertia\Inertia;

class Edit extends Controller
{
    public function __invoke(Watcher $watcher)
    {
        return Inertia::render('Watcher/Form', [
            'type' => 'Update',
            'intervals' => Interval::all()->toArray(),
            'regions' => Region::all()->toArray(),
            'watcher' => $watcher->toArray(),
        ]);
    }
}
