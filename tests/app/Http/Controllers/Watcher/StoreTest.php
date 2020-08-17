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
            'query' => 'some-class',
        ];

        $this->actingAs($user)->postJson(route('watcher.store'), $data)->assertSuccessful();

        $this->assertDatabaseHas('watchers', array_merge(
            $data,
            ['user_id' => $user->id]
        ));
    }

    /** @test */
    public function itWillAddTemplate(): void
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $data = [
            'name' => 'Foo',
            'url' => 'http://some-url.com/with/price',
            'query' => 'some-class',
            'xpath_name' => '//*[@id="asdf"]'
        ];

        $this->actingAs($user)->postJson(route('watcher.store'), $data)->assertSuccessful();

        $this->assertDatabaseHas('templates', [
            'domain' => 'some-url.com',
            'xpath_value' => 'some-class',
            'xpath_name' => '//*[@id="asdf"]',
            'user_id' => $user->id
        ]);
    }

}
