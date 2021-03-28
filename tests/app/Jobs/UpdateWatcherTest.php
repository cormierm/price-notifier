<?php

namespace Tests\App\Jobs;

use App\Events\WatcherCreatedOrUpdated;
use App\Jobs\SendPushoverMessage;
use App\Jobs\UpdateWatcher;
use App\Notifications\PriceAlert;
use App\Notifications\StockAlert;
use App\PriceChange;
use App\StockChange;
use App\Utils\Fetchers\BrowsershotFetcher;
use App\Utils\Fetchers\HtmlFetcher;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
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

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
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

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
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
        Notification::fake();

        $rawValue = '950.00';
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'value' => '1100.00',
            'alert_value' => '1000.00',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ]);
        $html = '<html><body><span class="value">' . $rawValue . '</span></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Notification::assertSentTo(
            $watcher->user,
            PriceAlert::class,
            function ($notification, $channels) use ($watcher) {
                return $notification->watcher->id === $watcher->id;
            }
        );
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

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
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

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
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

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('1129.99', $watcher->fresh()->lowest_price);
        $this->assertEquals(Carbon::now(), $watcher->fresh()->lowest_at);
    }

    /** @test */
    public function itWillSendStockAlertWhenChangedHasStock(): void
    {
        Notification::fake();

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => true,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Notification::assertSentTo(
            $watcher->user,
            StockAlert::class,
            function ($notification, $channels) use ($watcher) {
                return $notification->watcher->id === $watcher->id;
            }
        );

        $this->assertTrue($watcher->fresh()->has_stock);
    }

    /** @test */
    public function itWillNotSendStockAlertWhenStockAlertSetToFalse(): void
    {
        Notification::fake();

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Notification::assertNothingSent();
    }

    /** @test */
    public function itWillSetHasStockToTrueForTextContainsTrueWhenTextFound(): void
    {
        Notification::fake();

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Notification::assertNothingSent();
        $this->assertTrue($watcher->fresh()->has_stock);
    }

    /** @test */
    public function itWillSetHasStockToFalseForTextContainsTrue(): void
    {
        Notification::fake();

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'has_stock' => true,
        ]);
        $html = '<html><body><div id="stock">Out of stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Notification::assertNothingSent();

        $this->assertFalse($watcher->fresh()->has_stock);
    }

    /** @test */
    public function itWillSetHasStockToTrueForStockConditionMissingText(): void
    {
        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'Out of Stock.',
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_MISSING_TEXT,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
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
            'stock_condition' => Watcher::STOCK_CONDITION_MISSING_TEXT,
            'has_stock' => true,
        ]);
        $html = '<html><body><div id="stock">Out of Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $this->doesntExpectJobs(SendPushoverMessage::class);

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertFalse($watcher->fresh()->has_stock, 'Does not change has_stock to false');
    }

    /** @test */
    public function itCreatesPriceChangeWhenNoPriceChangeExists(): void
    {
        Event::fake();

        $watcher = factory(Watcher::class)->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'query' => '//span[@class="value"]',
        ]);
        $html = '<html><span class="value">1119.99</span></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertDatabaseHas('price_changes', [
            'watcher_id' => $watcher->id,
            'price' => '1119.99',
        ]);
    }

    /** @test */
    public function itDoesNotCreatePriceChangeWhenPriceHasNotChanged(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $price = '1119.99';
        $watcher = factory(Watcher::class)->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'query' => '//span[@class="value"]',
        ]);
        factory(PriceChange::class)->create([
            'watcher_id' => $watcher->id,
            'price' => $price,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $html = '<html><span class="value">' . $price . '</span></html>';

        $this->mock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
            return $mock;
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertDatabaseMissing('price_changes', [
            'watcher_id' => $watcher->id,
            'price' => '1119.99',
            'created_at' => Carbon::now(),
        ]);
        $this->assertEquals(1, $watcher->priceChanges()->count());
    }

    /** @test */
    public function itDoesNotCreateStockChangeWhenStockHasNotChanged(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = factory(Watcher::class)->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
        ]);
        factory(StockChange::class)->create([
            'watcher_id' => $watcher->id,
            'stock' => true,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $html = '<html><div id="stock">In Stock.</div></html>';

        $this->mock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
            return $mock;
        });

        $this->assertEquals(1, $watcher->stockChanges()->count());

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertDatabaseMissing('price_changes', [
            'watcher_id' => $watcher->id,
            'stock' => true,
            'created_at' => Carbon::now(),
        ]);

        $this->assertEquals(1, $watcher->stockChanges()->count());
    }

    /** @test */
    public function itCreatePriceChangeWhenStockHasChanged(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $price = '1119.99';
        $watcher = factory(Watcher::class)->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'query' => '//span[@class="value"]',
            'xpath_stock' => '//div[@id="stock"]',
            'stock_text' => 'In Stock.',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
        ]);
        factory(StockChange::class)->create([
            'watcher_id' => $watcher->id,
            'stock' => false,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $html = '<html><span class="value">' . $price . '</span><div id="stock">In Stock.</div></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertDatabaseHas('stock_changes', [
            'watcher_id' => $watcher->id,
            'stock' => true,
            'created_at' => Carbon::now(),
        ]);
        $this->assertEquals(2, $watcher->stockChanges()->count());
    }

    /** @test */
    public function itCreatePriceChangeWhenPriceHasChanged(): void
    {
        Event::fake();
        Carbon::setTestNow('now');
        $newPrice = '222';
        $watcher = factory(Watcher::class)->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'query' => '//span[@class="value"]',
        ]);
        factory(PriceChange::class)->create([
            'watcher_id' => $watcher->id,
            'price' => '111',
            'created_at' => Carbon::now()->subDay(),
        ]);
        $html = '<html><span class="value">' . $newPrice . '</span></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertDatabaseHas('price_changes', [
            'watcher_id' => $watcher->id,
            'price' => $newPrice,
            'created_at' => Carbon::now(),
        ]);
        $this->assertEquals(2, $watcher->priceChanges()->count());
    }
}
