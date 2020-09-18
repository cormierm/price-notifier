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

            if (!$watcher->interval->minutes ||
                (!$watcher->region && !config('pcn.fetcher.fetch_null_regions')) ||
                (config('pcn.region') && $watcher->region && config('pcn.region') !== $watcher->region->name) ||
                (!config('pcn.region') && $watcher->region)
            ) {
                return;
            }

            // delay for browsershot to avoid request error state
            if (config('app.env') !== 'testing' && $watcher->client === HtmlFetcher::CLIENT_BROWERSHOT) {
                sleep(config('pcn.fetcher.delay'));
            }

            if (!$lastLog
                || $watcher->interval->minutes === 1
                || $lastLog->created_at < Carbon::now()->subMinutes($watcher->interval->minutes)
            ) {
                UpdateWatcher::dispatch($watcher);
            }
        });
    }
}
