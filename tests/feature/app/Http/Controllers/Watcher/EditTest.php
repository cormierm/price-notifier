<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

use App\Models\User;
use App\Models\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Watcher/Form')
                ->has('watcher')
                ->where('watcher.id', $watcher->id)
            );
    }
}
