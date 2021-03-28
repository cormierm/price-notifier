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
}
