<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __invoke(Request $request)
    {

        return view('template.index', [
            'templates' => $request->user()->templates,
        ]);
    }
}
