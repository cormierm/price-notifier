<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Index extends Controller
{
    public function __invoke(Request $request)
    {

        return Inertia::render('Template/Index', [
            'templates' => $request->user()->templates->toArray(),
        ]);
    }
}
