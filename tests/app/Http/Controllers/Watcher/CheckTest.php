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
    public function itReturnsTitleAndFormattedPriceFromXPath(): void
    {
        $user = User::factory()->create();
        $xpath = '//span[@id="foobar"]';
        $html = '<html><title>Taco Salad</title><body><span id="foobar">CDN$ 55.00</span></body></html>';

        $this->mockHtmlFetcher($html);

        $this->actingAs($user)->post(route('watcher.check'), [
            'url' => 'http://foobar.com',
            'price_query' => $xpath,
            'price_query_type' => 'xpath',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ])
            ->assertSuccessful()
            ->assertJson([
                'value' => '55.00',
                'title' => 'Taco Salad',
            ]);
    }

    /** @test */
    public function itReturnsDebugInformation(): void
    {
        $user = User::factory()->create();
        $priceQuery = '//span[@id="foobar"]';
        $xpathStock = '//span[@id="stock"]';
        $html = '<html><body><span id="foobar">CDN$ 55.00</span><span id="stock">Out of Stock</span></body></html>';

        $this->mockHtmlFetcher($html);

        $this->actingAs($user)->post(route('watcher.check'), [
            'url' => 'http://foobar.com',
            'price_query' => $priceQuery,
            'price_query_type' => 'xpath',
            'stock_query' => $xpathStock,
            'stock_query_type' => 'xpath',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ])
            ->assertSuccessful()
            ->assertJson([
                'debug' => [
                    'value_inner_text' => 'CDN$ 55.00',
                    'stock_inner_text' => 'Out of Stock',
                    'stock_outer_html' => '<span id="stock">Out of Stock</span>'
                ]
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
