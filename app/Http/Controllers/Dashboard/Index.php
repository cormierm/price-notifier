<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __invoke(Request $request)
    {
        return view('dashboard.index', [
            'priceChanges' => $request->user()->priceChanges()->with('watcher')->latest('created_at')->limit(10)->get(),
            'stockChanges' => $request->user()->stockChanges()->with('watcher')->latest('created_at')->limit(10)->get(),
        ]);
    }
}
