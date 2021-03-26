<?php

namespace Tests\App\Http\Controllers\Api\Watcher;

use App\User;
use App\Utils\Fetchers\HtmlFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itWillAddWatcher(): void
    {
        $user = factory(User::class)->create();

        $data = [
            'url' => 'http://some.url',
            'name' => 'Foobar',
            'query' => '//span[id="price"]',
            'xpath_stock' => '//span[id="stock"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ];
        $this->postJson(route('api.watcher.store'), $data, [
            "Authorization" => "Basic " . base64_encode($user->email . ":" . $user->api_key),
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('watchers', [
            'user_id' => $user->id,
            'url' => $data['url'],
            'query' => $data['query'],
            'xpath_stock' => $data['xpath_stock'],
            'client' => $data['client']
        ]);
    }

    /** @test */
    public function itWillReturn401ForInvalidCredentials(): void
    {
        $user = factory(User::class)->create();

        $this->postJson(route('api.watcher.store'), [], [
            "Authorization" => "Basic " . base64_encode($user->email . ":not-a-api-key"),
        ])
            ->assertStatus(401);
    }
}
