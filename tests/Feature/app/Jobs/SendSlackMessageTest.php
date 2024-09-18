<?php

namespace Tests\App\Jobs;

use App\Jobs\SendSlackMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendSlackMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanSendPushoverMessage(): void
    {
        $this->markTestSkipped();
        
        SendSlackMessage::dispatch('Testing 1...2...3...4...');
    }
}
