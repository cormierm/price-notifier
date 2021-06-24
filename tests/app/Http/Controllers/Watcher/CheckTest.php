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
        $user = factory(User::class)->create();
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
        $user = factory(User::class)->create();
        $xpathValue = '//span[@id="foobar"]';
        $xpathStock = '//span[@id="stock"]';
        $html = '<html><body><span id="foobar">CDN$ 55.00</span><span id="stock">Out of Stock</span></body></html>';

        $this->mockHtmlFetcher($html);

        $this->actingAs($user)->post(route('watcher.check'), [
            'url' => 'http://foobar.com',
            'price_query' => $xpathValue,
            'price_query_type' => 'xpath',
            'xpath_stock' => $xpathStock,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ])
            ->assertSuccessful()
            ->assertJson([
                'debug' => [
                    'value_inner_text' => 'CDN$ 55.00',
                    'stock_inner_text' => 'Out of Stock',
                    'stock_html' => '<span id="stock">Out of Stock</span>'
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
