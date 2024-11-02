<?php

namespace Tests\Feature\App\Jobs;

use App\Events\WatcherCreatedOrUpdated;
use App\Jobs\SendPushoverMessage;
use App\Jobs\SendSlackMessage;
use App\Jobs\UpdateWatcher;
use App\Models\PriceChange;
use App\Models\StockChange;
use App\Models\Watcher;
use App\Utils\Fetchers\BrowsershotFetcher;
use App\Utils\Fetchers\HtmlFetcher;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateWatcherTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanUpdateWatcherWithXPathQuery(): void
    {
        Event::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//div[@id="pull-right-price"]/span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
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
    public function itCanUpdateWatcherWithRegexQuery(): void
    {
        Event::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '/<span class="value">(.*?)<\/span>/',
            'price_query_type' => Watcher::QUERY_TYPE_REGEX,
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
        Event::fake();

        $region = 'kitchener';
        Config::set('pcn.region', $region);
        $rawValue = 'CDN$ 149.99';
        $watcher = Watcher::factory()->create([
            'price_query' => '//div[@id="pull-right-price"]/span[@class="value"]',
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
        Event::fake();

        $watcher = Watcher::factory()->create([
            'url' => 'asdf://not.a.valid.url/foobar'
        ]);

        UpdateWatcher::dispatch($watcher);

        $this->assertNotNull($watcher->logs->first()->error);
    }

    /** @test */
    public function itSendsPriceAlertWhenValueIsLessThanAlertValue(): void
    {
        Bus::fake();
        $rawValue = '950.00';
        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
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

        Bus::assertDispatched(SendPushoverMessage::class);
        Bus::assertNotDispatched(SendSlackMessage::class);
    }

    /** @test */
    public function itSendsPriceAlertWhenValueIsGreaterThanAlertValue(): void
    {
        Bus::fake();
        $rawValue = '1950.00';
        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'value' => '1100.00',
            'alert_value' => '1000.00',
            'alert_condition' => Watcher::ALERT_CONDITION_GREATER_THAN,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ]);
        $html = '<html><body><span class="value">' . $rawValue . '</span></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Bus::assertDispatched(SendPushoverMessage::class);
        Bus::assertNotDispatched(SendSlackMessage::class);
    }

    /** @test */
    public function itWillSetLowestPriceIfNoneSet(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
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

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
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

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'lowest_price' => '2499.00'
        ]);
        $html = '<html><span class="value">749.99</span></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('749.99', $watcher->fresh()->lowest_price);
        $this->assertEquals(Carbon::now(), $watcher->fresh()->lowest_at);
    }

    /** @test */
    public function itWillSendStockAlertWhenChangedHasStockAndStockRequiresPriceIsTrueAndHasPrice(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'In Stock.',
            'stock_alert' => true,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_requires_price' => true,
            'has_stock' => false,
        ]);
        $html = '<html><body><span class="value">55.66</span><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertTrue($watcher->fresh()->has_stock);
        Bus::assertDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillSendStockAlertWhenChangedHasStockAndStockRequiresPriceIsFalseAndHasNoPrice(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'In Stock.',
            'stock_alert' => true,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_requires_price' => false,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertTrue($watcher->fresh()->has_stock);

        Bus::assertDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillNotSendStockAlertWhenChangedHasStockAndStockRequiresPriceIsTrueAndHasNoPrice(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'In Stock.',
            'stock_alert' => true,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_requires_price' => true,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertFalse($watcher->fresh()->has_stock);
        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillNotSendStockAlertWhenStockAlertSetToFalse(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => 'In Stock.',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_requires_price' => false,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillSetHasStockToTrueForTextContainsTrueWhenTextFound(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
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

        $this->assertTrue($watcher->fresh()->has_stock);
        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillSetHasStockToTrueForTextContainsWithSelectorQuery(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '#stock',
            'stock_query_type' => Watcher::QUERY_TYPE_SELECTOR,
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

        $this->assertTrue($watcher->fresh()->has_stock);
        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillSetHasStockToFalseForTextContainsTrue(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
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

        $this->assertFalse($watcher->fresh()->has_stock);
        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillSetHasStockToTrueForStockConditionMissingText(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'Out of Stock.',
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_MISSING_TEXT,
            'has_stock' => false,
        ]);
        $html = '<html><body><div id="stock">In Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertTrue($watcher->fresh()->has_stock, 'Does not change has_stock to true');
        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itWillSetHasStockToFalseForTextContainsFalse(): void
    {
        Bus::fake();

        $watcher = Watcher::factory()->create([
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'alert_value' => null,
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'Out of Stock.',
            'stock_alert' => false,
            'stock_condition' => Watcher::STOCK_CONDITION_MISSING_TEXT,
            'has_stock' => true,
        ]);
        $html = '<html><body><div id="stock">Out of Stock.</div></body></html>';

        $this->partialMock(BrowsershotFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            return $mock->shouldReceive('fetchHtml')->with($watcher->url, $watcher->user->user_agent)->andReturn($html);
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertFalse($watcher->fresh()->has_stock, 'Does not change has_stock to false');

        Bus::assertNotDispatched(SendPushoverMessage::class);
    }

    /** @test */
    public function itCreatesPriceChangeWhenNoPriceChangeExists(): void
    {
        Event::fake();

        $watcher = Watcher::factory()->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'price_query' => '//span[@class="value"]',
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
        $watcher = Watcher::factory()->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'price_query' => '//span[@class="value"]',
        ]);
        PriceChange::factory()->create([
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

        $watcher = Watcher::factory()->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'In Stock.',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
        ]);
        StockChange::factory()->create([
            'watcher_id' => $watcher->id,
            'stock' => true,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $html = '<html><span class="value">$11.45</span><div id="stock">In Stock.</div></html>';

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
    public function itDoesNotCreateStockChangeWhenStockRequiresPrice(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = Watcher::factory()->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'In Stock.',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
            'stock_requires_price' => true,
        ]);
        StockChange::factory()->create([
            'watcher_id' => $watcher->id,
            'stock' => false,
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
    public function itCreatesStockChangeWhenStockHasChanged(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = Watcher::factory()->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'price_query' => '//span[@class="value"]',
            'price_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_query' => '//div[@id="stock"]',
            'stock_query_type' => Watcher::QUERY_TYPE_XPATH,
            'stock_text' => 'In Stock.',
            'stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT,
        ]);
        StockChange::factory()->create([
            'watcher_id' => $watcher->id,
            'stock' => false,
            'created_at' => Carbon::now()->subDay(),
        ]);
        $html = '<html><span class="value">111.00</span><div id="stock">In Stock.</div></html>';

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
        $watcher = Watcher::factory()->create([
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
            'price_query' => '//span[@class="value"]',
        ]);
        PriceChange::factory()->create([
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
