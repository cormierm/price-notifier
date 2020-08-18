<?php

namespace Tests\App\Http\Controllers\Watcher;

use App\User;
use App\Utils\HtmlFetcher;
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
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $xpath = '//span[@id="foobar"]';
        $xpathTitle = '//h1[@class="title"]';
        $html = '<html><body><h1 class="title">Taco Salad</h1><span id="foobar">CDN$ 55.00</span></body></html>';

        $this->mockHtmlFetcher($html);

        $this->actingAs($user)->post(route('watcher.check'), [
            'url' => 'http://foobar.com',
            'xpath_value' => $xpath,
            'xpath_name' => $xpathTitle,
        ])
            ->assertSuccessful()
            ->assertJson([
                'title' => 'Taco Salad',
            ]);
    }

    private function mockHtmlFetcher(string $html)
    {
        $this->mock(HtmlFetcher::class, function (MockInterface $mock) use ($html) {
            $mock->shouldReceive('getHtmlFromUrl')->andReturn($html);

            return $mock;
        });
    }
}
