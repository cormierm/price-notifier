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
        $region = factory(Region::class)->create();
        $watcher = factory(Watcher::class)->create([
            'region_id' => $region->id,
            'interval_id' => $interval->id,
        ]);
        Config::set('pcn.region', $region->name);

        factory(WatcherLog::class)->create([
            'watcher_id' => $watcher->id,
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
        $region = factory(Region::class)->create();
        factory(Watcher::class)->create([
            'region_id' => $region->id,
            'interval_id' => $interval->id,
        ]);
        Config::set('pcn.region', $region->name);

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
    public function itWillFetchWatcherInRegion(): void
    {
        $region = factory(Region::class)->create();
        factory(Watcher::class)->create([
            'region_id' => $region->id,
        ]);
        Config::set('pcn.region', $region->name);

        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itWillNotFetchWatcherInAnotherRegion(): void
    {
        $region = factory(Region::class)->create();
        factory(Watcher::class)->create([
            'region_id' => $region->id,
        ]);
        Config::set('pcn.region', 'foo-bar-region');

        $this->doesntExpectJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itWillFetchWatcherWithNullRegion(): void
    {
        factory(Watcher::class)->create([
            'region_id' => null
        ]);
        Config::set('pcn.region', 'foo-bar-region');
        Config::set('pcn.fetcher.fetch_null_regions', true);

        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itWillNotFetchWatcherWithNullRegion(): void
    {
        factory(Watcher::class)->create([
            'region_id' => null
        ]);
        Config::set('pcn.region', 'foo-bar-region');
        Config::set('pcn.fetcher.fetch_null_regions', false);

        $this->doesntExpectJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }
}
