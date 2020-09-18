<?php

namespace Tests\App\Jobs;

use App\Events\WatcherCreatedOrUpdated;
use App\Jobs\SendPushoverMessage;
use App\Jobs\UpdateWatcher;
use App\Utils\HtmlFetcher;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateWatcherTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateWatcher(): void
    {
        Event::fake();

        $watcher = factory(Watcher::class)->create([
            'query' => '//div[@id="pull-right-price"]/span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ]);
        $html = '<html><body><div id="pull-right-price" class="pull-right "><span class="value">149.99</span><span class="currency">$</span></div></div></body></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        UpdateWatcher::dispatch($watcher);
        Event::assertDispatched(WatcherCreatedOrUpdated::class);

        $this->assertEquals('149.99', $watcher->fresh()->value);
    }

    /** @test */
    public function itCreatesLog(): void
    {
        $region = 'kitchener';
        Config::set('pcn.region', $region);
        $rawValue = 'CDN$ 149.99';
        $watcher = factory(Watcher::class)->create([
            'query' => '//div[@id="pull-right-price"]/span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT
        ]);
        $html = '<html><body><div id="pull-right-price" class="pull-right "><span class="value">' . $rawValue . '</span><span class="currency">$</span></div></div></body></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        UpdateWatcher::dispatch($watcher);

        $this->assertDatabaseHas('watcher_logs', [
            'watcher_id' => $watcher->id,
            'formatted_value' => '149.99',
            'raw_value' => $rawValue,
            'region' => $region,
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

    /** @test */
    public function itSendsPriceAlertWhenValueIsLessThanAlertValue(): void
    {
        $rawValue = '50.00';
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'value' => '110.00',
            'alert_value' => '100.00',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ]);
        $html = '<html><body><span class="value">' . $rawValue . '</span></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $this->expectsJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();
    }

    /** @test */
    public function itWillSetLowestPriceIfNoneSet(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ]);
        $html = '<html><span class="value">1119.99</span></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('1119.99', $watcher->fresh()->lowest_price);
        $this->assertEquals(Carbon::now(), $watcher->fresh()->lowest_at);
    }

    /** @test */
    public function itCannotSetLowestPriceIfPriceIsHigher(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'lowest_price' => '1.00'
        ]);
        $html = '<html><span class="value">9.99</span></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('1.00', $watcher->fresh()->lowest_price);
    }

    /** @test */
    public function itCanSetLowestPriceIfPriceIsLower(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'lowest_price' => '1299.00'
        ]);
        $html = '<html><span class="value">CDN$ 1,129.99</span></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('1129.99', $watcher->fresh()->lowest_price);
        $this->assertEquals(Carbon::now(), $watcher->fresh()->lowest_at);
    }

    /** @test */
    public function itWillSendStockAlertWhenChangedHasStock(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => true,
            'stock_contains' => true,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $this->expectsJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertTrue($watcher->fresh()->has_stock);
    }

    /** @test */
    public function itWillNotSendStockAlertWhenStockAlertSetToFalse(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => false,
            'stock_contains' => true,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $this->doesntExpectJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();
    }

    /** @test */
    public function itWillSetHasStockToTrueForTextContainsTrueWhenTextFound(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => false,
            'stock_contains' => true,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')
                ->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)
                ->andReturn($html);

            return $mock;
        });

        $this->doesntExpectJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertTrue($watcher->fresh()->has_stock);
    }

    /** @test */
    public function itWillSetHasStockToFalseForTextContainsTrue(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => false,
            'stock_contains' => true,
            'has_stock' => true,
        ]);
        $html = '<html><body><div id="stock">Out of stock.</div></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')
                ->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)
                ->andReturn($html);

            return $mock;
        });

        $this->doesntExpectJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertFalse($watcher->fresh()->has_stock);
    }

    /** @test */
    public function itWillSetHasStockToTrueForTextContainsFalse(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'Out of Stock.',
            'stock_alert' => false,
            'stock_contains' => false,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')
                ->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)
                ->andReturn($html);

            return $mock;
        });

        $this->doesntExpectJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertTrue($watcher->fresh()->has_stock, 'Does not change has_stock to true');
    }

    /** @test */
    public function itWillSetHasStockToFalseForTextContainsFalse(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'Out of Stock.',
            'stock_alert' => false,
            'stock_contains' => false,
            'has_stock' => true,
        ]);
        $html = '<html><body><div id="stock">Out of Stock.</div></body></html>';

        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')
                ->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)
                ->andReturn($html);

            return $mock;
        });

        $this->doesntExpectJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertFalse($watcher->fresh()->has_stock, 'Does not change has_stock to false');
    }
}
