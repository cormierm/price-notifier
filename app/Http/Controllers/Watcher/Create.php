<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;

class Create extends Controller
{
    public function __invoke()
    {
        return view('watcher.create');
    }
}
