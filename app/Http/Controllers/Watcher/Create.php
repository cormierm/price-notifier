<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Models\Interval;
use App\Models\Region;
use Inertia\Inertia;

class Create extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Watcher/Form', [
            'type' => 'Create',
            'intervals' => Interval::all()->toArray(),
            'regions' => Region::all()->toArray(),
        ]);
    }
}
