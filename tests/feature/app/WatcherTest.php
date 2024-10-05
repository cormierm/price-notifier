<?php

namespace Tests\Feature\App;

use App\Models\Watcher;
use App\Models\WatcherLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatcherTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itWillGetLastLog(): void
    {
        $watcher = Watcher::factory()->create();

        WatcherLog::factory()->create([
            'watcher_id' => $watcher->id,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $lastLog = WatcherLog::factory()->create([
            'watcher_id' => $watcher->id,
        ]);
        WatcherLog::factory()->create([
            'watcher_id' => $watcher->id,
            'created_at' => Carbon::now()->subMinute(),
        ]);
        WatcherLog::factory()->create([
            'watcher_id' => $watcher->id,
            'created_at' => Carbon::now()->subHour(),
        ]);

        $this->assertEquals($lastLog->fresh(), $watcher->lastLog());
    }

    /** @test */
    public function itWillReturnNullIfNotLog(): void
    {
        $watcher = Watcher::factory()->create();

        $this->assertNull($watcher->lastLog());
    }

    /** @test */
    public function itCanParseDomainFromUrl(): void
    {
        $watcher = Watcher::factory()->create([
            'url' => 'https://www.foobar.com/something/else/index.html',
        ]);

        $this->assertEquals('foobar.com', $watcher->urlDomain());
    }

    /** @test */
    public function itReturnsStatusErrorIfLastLogHasError(): void
    {
        $log = WatcherLog::factory()->create([
            'error' => 'Some error'
        ]);

        $this->assertEquals('error', $log->watcher->status);
    }

    /** @test */
    public function itReturnsStatusDisabledIfNoIntervalSet(): void
    {
        $watcher = Watcher::factory()->disabled()->create();

        $this->assertEquals('disabled', $watcher->status);
    }

    /** @test */
    public function itReturnsStatusOkayIfNoLastLogErrorAndIntervalHasMinutesSet(): void
    {
        $watcher = Watcher::factory()->create();

        $this->assertEquals('ok', $watcher->status);
    }
}
