<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use NotificationChannels\Twilio\TwilioConfig;
use Tests\TestCase;
use Twilio\Rest\Client as TwilioService;

class TwilioServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itUsesLoggedInUserTwilioCredsForTwilioService()
    {
        $user = factory(User::class)->create();
        Auth::login($user);

        $twilioConfig = app(TwilioConfig::class);
        $twilioClient = app(TwilioService::class);

        $this->assertEquals($user->twilio_auth_token, $twilioClient->getPassword());
        $this->assertEquals($user->twilio_account_sid, $twilioClient->getUsername());
        $this->assertEquals($user->twilio_from, $twilioConfig->getAlphanumericSender());
    }
}
