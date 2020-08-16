<?php

namespace App\Http\Controllers;

use App\Http\Resources\WatcherResource;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request): Renderable
    {
        return view('home', [
            'watchers' => WatcherResource::collection($request->user()->watchers)
        ]);
    }
}
