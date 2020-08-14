<?php

namespace Tests\App\Http\Controllers\Watcher;

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
        $user = factory(User::class)->create();
        $watcher = factory(Watcher::class)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->get(route('watcher.edit', $watcher->id))
            ->assertSuccessful()
            ->assertViewIs('watcher.edit')
            ->assertViewHas('watcher', $watcher);
    }

}
