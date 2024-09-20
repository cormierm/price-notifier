<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\Models\User;
use App\Models\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowsViewWithWatchers(): void
    {
        $user = User::factory()->create();
        Watcher::factory(3)->create();

        $this->actingAs($user)->get(route('watcher.index'))
            ->assertSuccessful()
            ->assertViewIs('watcher.index');
    }
}
