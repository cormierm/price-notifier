<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\Models\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowsViewWithWatcher(): void
    {
        $watcher = Watcher::factory()->create();

        $this->actingAs($watcher->user)->get(route('watcher.show', $watcher))
            ->assertSuccessful()
            ->assertViewIs('watcher.show')
            ->assertViewHas('watcher', $watcher);
    }
}
