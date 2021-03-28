<?php

namespace Tests\Feature\Notifications;

use App\Notifications\PriceAlert;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NotificationChannels\Pushover\PushoverChannel;
use Tests\TestCase;
use function factory;

class PriceAlertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itWillSendToPushover()
    {
        $watcher = factory(Watcher::class)->create();

        $this->assertContains(PushoverChannel::class, (new PriceAlert($watcher))->via($watcher->user));
    }

    /** @test */
    public function itWillCreatePushoverMessage()
    {
        $watcher = factory(Watcher::class)->create();
        $newPrice = '123';
        $watcher->priceChanges()->create([
            'price' => $newPrice,
        ]);

        $pushoverMessage = (new PriceAlert($watcher))->toPushover($watcher->user);

        $this->assertEquals($watcher->url, $pushoverMessage->url);
        $this->assertEquals('Price Alert!', $pushoverMessage->title);
        $this->assertEquals("{$watcher->name}\n" . number_format($newPrice, 2), $pushoverMessage->content);
        $this->assertEquals('cashregister', $pushoverMessage->sound);
    }
}
