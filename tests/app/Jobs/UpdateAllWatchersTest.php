<?php

namespace Tests\App\Jobs;

use App\Interval;
use App\Jobs\UpdateAllWatchers;
use App\Jobs\UpdateWatcher;
use App\Region;
use App\Watcher;
use App\WatcherLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UpdateAllWatchersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itUpdatesWatcher(): void
    {
        $minutes = 30;
        $interval = factory(Interval::class)->create([
            'minutes' => $minutes,
        ]);
        $watchers = factory(Watcher::class)->create([
            'interval_id' => $interval->id,
        ]);

        factory(WatcherLog::class)->create([
            'watcher_id' => $watchers->first()->id,
            'created_at' => Carbon::now()->subMinutes($minutes + 5)
        ]);
        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itUpdatesWithNoWatcherLog(): void
    {
        $minutes = 30;
        $interval = factory(Interval::class)->create([
            'minutes' => $minutes,
        ]);
        factory(Watcher::class)->create([
            'interval_id' => $interval->id,
        ]);

        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itDoesNotUpdateWatcherWithNullIntervalMinutes(): void
    {
        $interval = factory(Interval::class)->create([
            'minutes' => null,
        ]);
        factory(Watcher::class)->create([
            'interval_id' => $interval->id,
        ]);

        $this->doesntExpectJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itOnlyRunsWatchersInRegion(): void
    {
        $region = factory(Region::class)->create();
        $watcher = factory(Watcher::class)->create([
            'region_id' => $region->id,
        ]);
        Config::set('pcn.region', $region->name);
        factory(Watcher::class, 2)->create();

        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }
}
