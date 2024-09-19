<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\User;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itShowsViewWithWatcher(): void
    {
        $user = User::factory()->create();
        $watcher = Watcher::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(route('watcher.edit', $watcher->id))
            ->assertSuccessful()
            ->assertViewIs('watcher.edit')
            ->assertViewHas('watcher', $watcher);
    }
}
