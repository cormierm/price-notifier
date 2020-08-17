<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\Http\Controllers\Watcher\Show;
use App\User;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowsViewWithWatcher(): void
    {
        $watcher = factory(Watcher::class)->create();

        $this->actingAs($watcher->user)->get(route('watcher.show', $watcher))
            ->assertSuccessful()
            ->assertViewIs('watcher.show')
            ->assertViewHas('watcher', $watcher);
    }

}
