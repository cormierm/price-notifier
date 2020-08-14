<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request): Renderable
    {
        return view('home', [
            'watchers' => $request->user()->watchers()->with('interval')->get()
        ]);
    }
}
