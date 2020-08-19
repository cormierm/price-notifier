<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\User;
use App\Utils\HtmlFetcher;
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
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ];

        $this->actingAs($user)->postJson(route('watcher.store'), array_merge(
            $data,
            [
                'xpath_name' => 'some-class',
            ]
        ))->assertSuccessful();

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
            'xpath_name' => '//*[@id="asdf"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ];

        $this->actingAs($user)->postJson(route('watcher.store'), $data)->assertSuccessful();

        $this->assertDatabaseHas('templates', [
            'domain' => 'some-url.com',
            'xpath_value' => 'some-class',
            'xpath_name' => '//*[@id="asdf"]',
            'user_id' => $user->id,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ]);
    }
}
