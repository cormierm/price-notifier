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
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class UpdateAllWatchersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itUpdatesWatcher(): void
    {
        Bus::fake();

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

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itUpdatesWithNoWatcherLog(): void
    {
        Bus::fake();
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

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itDoesNotUpdateWatcherWithNullIntervalMinutes(): void
    {
        Bus::fake();

        $interval = Interval::factory()->create([
            'minutes' => null,
        ]);
        Watcher::factory()->create([
            'interval_id' => $interval->id,
        ]);

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertNotDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itWillFetchWatcherInRegion(): void
    {
        Bus::fake();

        $region = Region::factory()->create();
        Watcher::factory()->create([
            'region_id' => $region->id,
        ]);
        Config::set('pcn.region', $region->name);

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itWillNotFetchWatcherInAnotherRegion(): void
    {
        Bus::fake();

        $region = Region::factory()->create();
        Watcher::factory()->create([
            'region_id' => $region->id,
        ]);
        Config::set('pcn.region', 'foo-bar-region');

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertNotDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itWillFetchWatcherWithNullRegion(): void
    {
        Bus::fake();

        Watcher::factory()->create([
            'region_id' => null
        ]);
        Config::set('pcn.region', 'foo-bar-region');
        Config::set('pcn.fetcher.fetch_null_regions', true);

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itWillNotFetchWatcherWithNullRegion(): void
    {
        Bus::fake();

        Watcher::factory()->create([
            'region_id' => null
        ]);
        Config::set('pcn.region', 'foo-bar-region');
        Config::set('pcn.fetcher.fetch_null_regions', false);

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertNotDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itWillUpdateWatcherIfIntervalMinutesIsOne(): void
    {
        Bus::fake();

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

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertDispatched(UpdateWatcher::class);
    }

    /** @test */
    public function itWillUpdateWatcherIfRegionIsAll(): void
    {
        Bus::fake();

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

        $job = new UpdateAllWatchers;
        $job->handle();

        Bus::assertDispatched(UpdateWatcher::class);
    }
}
