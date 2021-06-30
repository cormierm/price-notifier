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
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $data = [
            'url' => 'http://some.url',
            'name' => 'Foobar',
            'price_query' => '//span[id="price"]',
            'price_query_type' => 'regex',
            'stock_query' => '//span[id="stock"]',
            'stock_query_type' => 'xpath',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ];
        $this->postJson(route('api.watcher.store'), $data, [
            "Authorization" => "Basic " . base64_encode($user->email . ":" . $user->api_key),
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('watchers', [
            'user_id' => $user->id,
            'url' => $data['url'],
            'price_query' => $data['price_query'],
            'price_query_type' => $data['price_query_type'],
            'stock_query' => $data['stock_query'],
            'stock_query_type' => $data['stock_query_type'],
            'client' => $data['client']
        ]);
    }

    /** @test */
    public function itWillReturn401ForInvalidCredentials(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('api.watcher.store'), [], [
            "Authorization" => "Basic " . base64_encode($user->email . ":not-a-api-key"),
        ])
            ->assertStatus(401);
    }
}
