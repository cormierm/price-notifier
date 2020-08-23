<?php

namespace App\Jobs;

use App\Utils\HtmlFetcher;
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

        $watchers->each(function ($watcher) {

            $lastLog = $watcher->lastLog();

            if (!$watcher->interval->minutes) {
                return;
            }

            // delay for browsershot to avoid request error state
            if ($watcher->client === HtmlFetcher::CLIENT_BROWERSHOT) {
                sleep(config('pcn.fetcher.delay'));
            }

            if (!$lastLog || $lastLog->created_at < Carbon::now()->subMinutes($watcher->interval->minutes)) {
                UpdateWatcher::dispatch($watcher);
            }
        });
    }
}
