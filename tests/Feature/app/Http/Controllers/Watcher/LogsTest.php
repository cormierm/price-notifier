<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\Models\Watcher;
use App\Models\WatcherLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itReturnsLogs(): void
    {
        $watcher = Watcher::factory()->create();
        $logs = WatcherLog::factory(3)->create([
            'watcher_id' => $watcher->id
        ]);

        $this->actingAs($watcher->user)->get(route('watcher.logs', $watcher))
            ->assertSuccessful()
            ->assertJsonCount(3)
            ->assertJson($logs->toArray());
    }

    /** @test */
    public function itReturnsLimitedLogs(): void
    {
        $watcher = Watcher::factory()->create();
        WatcherLog::factory(10)->create([
            'watcher_id' => $watcher->id
        ]);

        $this->actingAs($watcher->user)->get(route('watcher.logs', [
            'watcher' => $watcher->id,
            'limit' => 5,
        ]))
            ->assertSuccessful()
            ->assertJsonCount(5);
    }
}
