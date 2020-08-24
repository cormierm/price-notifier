<?php

namespace Tests\App\Jobs;

use App\Events\WatcherCreatedOrUpdated;
use App\Jobs\SendPushoverMessage;
use App\Jobs\UpdateWatcher;
use App\Utils\HtmlFetcher;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function itSendsAlertWhenValueIsLessThanAlertValue(): void
    {
//        $user = factory(User::class)->create([
//            'pushover_user_key' => env('PUSHOVER_USER_KEY'),
//            'pushover_api_token' => env('PUSHOVER_API_TOKEN'),
//        ]);
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
    public function itCanSetLowestPriceIfNoneSet(): void
    {
        Event::fake();
        Carbon::setTestNow('now');

        $watcher = factory(Watcher::class)->create([
            'query' => '//span[@class="value"]',
            'client' => HtmlFetcher::CLIENT_BROWERSHOT,
        ]);
        $html = '<html><span class="value">9.99</span></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('9.99', $watcher->fresh()->lowest_price);
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
            'lowest_price' => '99.00'
        ]);
        $html = '<html><span class="value">9.99</span></html>';


        $this->mock(HtmlFetcher::class, function (MockInterface  $mock) use ($html, $watcher) {
            $mock->shouldReceive('getHtmlFromUrl')->with($watcher->url, HtmlFetcher::CLIENT_BROWERSHOT)->andReturn($html);

            return $mock;
        });

        $job = new UpdateWatcher($watcher);
        $job->handle();

        $this->assertEquals('9.99', $watcher->fresh()->lowest_price);
        $this->assertEquals(Carbon::now(), $watcher->fresh()->lowest_at);
    }
}
