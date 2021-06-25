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
        $interval = Interval::factory()->create([
            'minutes' => $minutes,
        ]);
        $region = Region::factory()->create();
        $watcher = Watcher::factory()->create([
            'region_id' => $region->id,
            'interval_id' => $interval->id,
        ]);
        Config::set('pcn.region', $region->name);

        WatcherLog::factory()->create([
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
        $interval = Interval::factory()->create([
            'minutes' => $minutes,
        ]);
        $region = Region::factory()->create();
        Watcher::factory()->create([
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
        $interval = Interval::factory()->create([
            'minutes' => null,
        ]);
        Watcher::factory()->create([
            'interval_id' => $interval->id,
        ]);

        $this->doesntExpectJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itWillFetchWatcherInRegion(): void
    {
        $region = Region::factory()->create();
        Watcher::factory()->create([
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
        $region = Region::factory()->create();
        Watcher::factory()->create([
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
        Watcher::factory()->create([
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
        Watcher::factory()->create([
            'region_id' => null
        ]);
        Config::set('pcn.region', 'foo-bar-region');
        Config::set('pcn.fetcher.fetch_null_regions', false);

        $this->doesntExpectJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itWillUpdateWatcherIfIntervalMinutesIsOne(): void
    {
        $minutes = 1;
        $interval = Interval::factory()->create([
            'minutes' => $minutes,
        ]);
        $region = Region::factory()->create();
        $watcher = Watcher::factory()->create([
            'region_id' => $region->id,
            'interval_id' => $interval->id,
        ]);
        Config::set('pcn.region', $region->name);

        WatcherLog::factory()->create([
            'watcher_id' => $watcher->id,
            'created_at' => Carbon::now()
        ]);
        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }

    /** @test */
    public function itWillUpdateWatcherIfRegionIsAll(): void
    {
        $minutes = 1;
        $interval = Interval::factory()->create([
            'minutes' => $minutes,
        ]);
        $region = Region::factory()->create([
            'name' => 'all',
        ]);
        $watcher = Watcher::factory()->create([
            'region_id' => $region->id,
            'interval_id' => $interval->id,
        ]);

        WatcherLog::factory()->create([
            'watcher_id' => $watcher->id,
            'created_at' => Carbon::now()->subDay()
        ]);
        $this->expectsJobs(UpdateWatcher::class);

        $job = new UpdateAllWatchers;
        $job->handle();
    }
}
