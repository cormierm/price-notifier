<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\User;
use App\Utils\Fetchers\BrowsershotFetcher;
use App\Utils\Fetchers\HtmlFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CheckTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanGetValueForXPath(): void
    {
        $user = factory(User::class)->create();
        $xpath = '//span[@id="foobar"]';
        $html = '<html><body><span id="foobar">CDN$ 55.00</span></body></html>';

        $this->mockHtmlFetcher($html);

        $this->actingAs($user)->post(route('watcher.check'), [
            'url' => 'http://foobar.com',
            'xpath_value' => $xpath,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ])
            ->assertSuccessful()
            ->assertJson([
                'value' => '55.00',
                'raw_value' => 'CDN$ 55.00',
                'title' => '',
            ]);
    }

    /** @test */
    public function itCanGetTitleForXPath(): void
    {
        $user = factory(User::class)->create();
        $xpath = '//span[@id="foobar"]';
        $html = '<title>Taco Salad</title><html><body><h1 class="title">Taco Salad</h1><span id="foobar">CDN$ 55.00</span></body></html>';

        $this->mockHtmlFetcher($html);

        $this->actingAs($user)->post(route('watcher.check'), [
            'url' => 'http://foobar.com',
            'xpath_value' => $xpath,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ])
            ->assertSuccessful()
            ->assertJson([
                'title' => 'Taco Salad',
            ]);
    }

    private function mockHtmlFetcher(string $html)
    {
        $this->partialMock(BrowsershotFetcher::class, function (MockInterface $mock) use ($html) {
            $mock->shouldReceive('fetchHtml')->andReturn($html);

            return $mock;
        });
    }
}
