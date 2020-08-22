<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\WatcherLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDeletesWatcherAndLogs(): void
    {
        $log = factory(WatcherLog::class)->create();

        $this->actingAs($log->watcher->user)
            ->delete(route('watcher.destroy', $log->watcher))
            ->assertSuccessful();

        $this->assertDatabaseMissing('watchers', [
            'id' => $log->watcher->id
        ]);

        $this->assertDatabaseMissing('watcher_logs', [
            'id' => $log->id
        ]);
    }


}
