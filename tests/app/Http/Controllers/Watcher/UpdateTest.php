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

    /** @test */
    public function itWillUpdateTemplate(): void
    {
        $watcher = factory(Watcher::class)->create();

        $query = '//some-class';
        $this->actingAs($watcher->user)->putJson(route('watcher.update', $watcher), [
            'query' => $query,
            'update_queries' => true,
        ])->assertSuccessful();

        $this->assertDatabaseHas('templates', [
            'domain' => $watcher->urlDomain(),
            'xpath_value' => $query,
        ]);
    }

    /** @test */
    public function itWillNotUpdateTemplate(): void
    {
        $watcher = factory(Watcher::class)->create();

        $query = '//some-class';
        $this->actingAs($watcher->user)->putJson(route('watcher.update', $watcher), [
            'query' => $query,
            'update_queries' => false,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('templates', [
            'domain' => $watcher->urlDomain(),
            'xpath_value' => $query,
        ]);
    }
}
