<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanCreateWatcher(): void
    {
        $user = factory(User::class)->create();
        $data = [
            'name' => 'Foo',
            'url' => 'http://some-url.com/with/price',
            'query_type' => 'class',
            'query' => 'some-class',
        ];

        $this->actingAs($user)->postJson(route('watcher.store'), $data)->assertSuccessful();

        $this->assertDatabaseHas('watchers', array_merge(
            $data,
            ['user_id' => $user->id]
        ));
    }

}
