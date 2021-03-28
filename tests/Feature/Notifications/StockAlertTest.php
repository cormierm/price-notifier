<?php

namespace Tests\Feature\Notifications;

use App\Notifications\StockAlert;
use App\Watcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Twilio\TwilioChannel;
use Tests\TestCase;

class StockAlertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itWillSendToPushover()
    {
        $watcher = factory(Watcher::class)->create();

        $this->assertContains(PushoverChannel::class, (new StockAlert($watcher))->via($watcher->user));
    }

    /** @test */
    public function itWillSendToTwilio()
    {
        $watcher = factory(Watcher::class)->create();

        $this->assertContains(TwilioChannel::class, (new StockAlert($watcher))->via($watcher->user));
    }

    /** @test */
    public function itWillCreatePushoverMessage()
    {
        $watcher = factory(Watcher::class)->create();
        $newPrice = '123';
        $watcher->priceChanges()->create([
            'price' => $newPrice,
        ]);

        $pushoverMessage = (new StockAlert($watcher))->toPushover($watcher->user);

        $this->assertEquals($watcher->url, $pushoverMessage->url);
        $this->assertEquals('Stock Alert!', $pushoverMessage->title);
        $this->assertEquals("{$watcher->name}\n" . $newPrice, $pushoverMessage->content);
        $this->assertEquals('cashregister', $pushoverMessage->sound);
    }

    /** @test */
    public function itWillSendTwilioSMSMessage()
    {
        $watcher = factory(Watcher::class)->create();
        $newPrice = '123';
        $watcher->priceChanges()->create([
            'price' => $newPrice,
        ]);

        $twilioMessage = (new StockAlert($watcher))->toTwilio($watcher->user);

        $this->assertEquals("Stick Alert! {$watcher->name} @ {$newPrice}", $twilioMessage->content);
    }
}
