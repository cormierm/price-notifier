<?php

namespace App\Jobs;

use App\Watcher;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAllWatchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $watchers = Watcher::all();

        $watchers->each(function($watcher) {

            $lastLog = $watcher->lastLog();

            if (!$watcher->interval->minutes) {
                return;
            }

            if (!$lastLog || $lastLog->created_at < Carbon::now()->subMinutes($watcher->interval->minutes)) {
                UpdateWatcher::dispatch($watcher);
            }
        });
    }
}
