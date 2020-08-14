<?php

namespace Tests\App\Jobs;

use App\Jobs\UpdateWatcher;
use App\Utils\HtmlFetcher;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateWatcherTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateWatcher(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query_type' => 'query',
            'query' => '//div[@id="pull-right-price"]/span[@class="value"]',
        ]);
        $html = '<html><body><div id="pull-right-price" class="pull-right "><span class="value">149.99</span><span class="currency">$</span></div></div></body></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url)->andReturn($html);

            return $mock;
        });

        UpdateWatcher::dispatch($watcher);

        $this->assertEquals('149.99', $watcher->fresh()->value);
    }

    /** @test */
    public function itCreatesLog(): void
    {
        $rawValue = 'CDN$ 149.99';
        $watcher = factory(Watcher::class)->create([
            'query_type' => 'query',
            'query' => '//div[@id="pull-right-price"]/span[@class="value"]',
        ]);
        $html = '<html><body><div id="pull-right-price" class="pull-right "><span class="value">' . $rawValue . '</span><span class="currency">$</span></div></div></body></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url)->andReturn($html);

            return $mock;
        });

        UpdateWatcher::dispatch($watcher);

        $this->assertDatabaseHas('watcher_logs', [
            'watcher_id' => $watcher->id,
            'formatted_value' => '149.99',
            'raw_value' => $rawValue,
        ]);
    }

    /** @test */
    public function itCreatesLogWithError(): void
    {
        $watcher = factory(Watcher::class)->create([
            'url' => 'asdf://not.a.valid.url/foobar'
        ]);

        UpdateWatcher::dispatch($watcher);

        $this->assertNotNull($watcher->logs->first()->error);
    }
}
