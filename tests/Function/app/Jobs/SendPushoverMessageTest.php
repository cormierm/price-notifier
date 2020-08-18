<?php

namespace Tests\App\Jobs;

use App\Jobs\SendPushoverMessage;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendPushoverMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itCanSendPushoverMessage(): void
    {
        $this->markTestSkipped();

        $user = factory(User::class)->create([
            'pushover_user_key' => env('PUSHOVER_USER_KEY'),
            'pushover_api_token' => env('PUSHOVER_API_TOKEN'),
        ]);

        SendPushoverMessage::dispatch($user, 'Some Title', 'Hellooooooo!', 'http://foobar.com/asdflkjdf');
    }

}
