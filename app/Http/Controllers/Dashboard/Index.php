<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardPriceChangeResource;
use App\Http\Resources\DashboardStockChangeResource;
use Illuminate\Http\Request;

class Index extends Controller
{
    public function __invoke(Request $request)
    {
        return view('dashboard.index', [
            'errors' => $request->user()->watcherLogs()
                ->whereNotNull('error')
                ->with('watcher')
                ->latest('created_at')
                ->limit(10)
                ->get(),
            'priceChanges' => DashboardPriceChangeResource::collection(
                $request->user()->priceChanges()
                    ->latest('created_at')
                    ->limit(10)
                    ->get()
            ),
            'stockChanges' => DashboardStockChangeResource::collection(
                $request->user()->stockChanges()
                    ->latest('created_at')
                    ->limit(10)
                    ->get()
            ),
        ]);
    }
}
