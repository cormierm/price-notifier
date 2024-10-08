<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\Models\PriceChange;
use App\Models\WatcherLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itDeletesWatcherPriceChangesAndLogs(): void
    {
        $log = WatcherLog::factory()->create();
        PriceChange::factory(2)->create([
            'watcher_id' => $log->watcher->id,
        ]);

        $this->actingAs($log->watcher->user)
            ->delete(route('watcher.destroy', $log->watcher))
            ->assertSuccessful();

        $this->assertDatabaseMissing('watchers', [
            'id' => $log->watcher->id
        ]);

        $this->assertDatabaseMissing('watcher_logs', [
            'id' => $log->id
        ]);

        $this->assertDatabaseMissing('price_changes', [
            'watcher_id' => $log->watcher->id
        ]);
    }
}
