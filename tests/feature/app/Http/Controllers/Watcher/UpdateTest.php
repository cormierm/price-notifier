<?php

namespace Tests\Feature\App\Http\Controllers\Watcher;

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
        $watcher = Watcher::factory()->create();

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
        $user = User::factory()->create();
        $watcher = Watcher::factory()->create();

        $data = [
            'name' => 'Foobar watcher',
        ];

        $this->actingAs($user)
            ->put(route('watcher.update', $watcher), $data)
            ->assertForbidden();
    }

    /** @test */
    public function itWillUpdateTemplate(): void
    {
        $watcher = Watcher::factory()->create();

        $query = '//some-class';
        $this->actingAs($watcher->user)->putJson(route('watcher.update', $watcher), [
            'price_query' => $query,
            'update_queries' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('templates', [
            'domain' => $watcher->urlDomain(),
            'price_query' => $query,
        ]);
    }

    /** @test */
    public function itWillNotUpdateTemplate(): void
    {
        $watcher = Watcher::factory()->create();

        $query = '//some-class';
        $this->actingAs($watcher->user)->putJson(route('watcher.update', $watcher), [
            'price_query' => $query,
            'update_queries' => false,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('templates', [
            'domain' => $watcher->urlDomain(),
            'price_query' => $query,
        ]);
    }
}
