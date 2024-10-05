<?php

namespace App\Jobs;

use App\Models\Watcher;
use App\Utils\Fetchers\HtmlFetcher;
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

            $intervalNotSet = !$watcher->interval->minutes;
            $doNotFetchNullRegion = !$watcher->region && !config('pcn.fetcher.fetch_null_regions');
            $watcherRegionDoesNotMatchServerRegion = config('pcn.region')
                && $watcher->region
                && config('pcn.region') !== $watcher->region->name
                && $watcher->region->name !== 'all';
            $serverRegionNotSetAndWatcherHasRegion = !config('pcn.region') && $watcher->region;

            if ($intervalNotSet ||
                $doNotFetchNullRegion ||
                $watcherRegionDoesNotMatchServerRegion ||
                $serverRegionNotSetAndWatcherHasRegion
            ) {
                return;
            }

            // delay for browsershot to avoid request error state
            if (config('app.env') !== 'testing' && $watcher->client === HtmlFetcher::CLIENT_BROWERSHOT) {
                sleep(config('pcn.fetcher.delay'));
            }

            if (!$lastLog
                || $watcher->interval->minutes == 1
                || $lastLog->created_at < Carbon::now()->subMinutes($watcher->interval->minutes)
            ) {
                UpdateWatcher::dispatch($watcher);
            }
        });
    }
}
