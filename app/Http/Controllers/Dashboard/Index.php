<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class Index extends Controller
{
    public function __invoke()
    {
        return view('dashboard.index');
    }
}
