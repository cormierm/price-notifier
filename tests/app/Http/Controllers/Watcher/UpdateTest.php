<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\User;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateWatcher(): void
    {
        $watcher = factory(Watcher::class)->create();

        $data = [
            'name' => 'Foobar watcher',
        ];

        $this->actingAs($watcher->user)
            ->put(route('watcher.update', $watcher), $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('watchers', [
            'id' => $watcher->id,
            'name' => $data['name']
        ]);
    }

    /** @test */
    public function itCannotUpdateAnotherUsersWatcher(): void
    {
        $user = factory(User::class)->create();
        $watcher = factory(Watcher::class)->create();

        $data = [
            'name' => 'Foobar watcher',
        ];

        $this->actingAs($user)
            ->put(route('watcher.update', $watcher), $data)
            ->assertForbidden();
    }
}
