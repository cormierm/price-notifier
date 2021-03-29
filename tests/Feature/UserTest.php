<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itWillSetPushoverConfig()
    {
        $user = factory(User::class)->create();

        $pushoverReceiver = $user->routeNotificationForPushover();

        $this->assertEquals($user->pushover_user_key, $pushoverReceiver->toArray()['user']);
        $this->assertEquals($user->pushover_api_token, $pushoverReceiver->toArray()['token']);
    }

    /** @test */
    public function itWillSetTwilioConfig()
    {
        $user = factory(User::class)->create();

        $this->assertEquals($user->phone_number, $user->routeNotificationForTwilio());
    }
}
