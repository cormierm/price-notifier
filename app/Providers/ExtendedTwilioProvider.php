<?php

namespace App\Providers;

use NotificationChannels\Twilio\TwilioConfig;
use NotificationChannels\Twilio\TwilioProvider;

class ExtendedTwilioProvider extends TwilioProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->bind(TwilioConfig::class, function () {
            return new TwilioConfig(
                array_merge(
                    $this->app['config']['twilio-notification-channel'],
                    [
                        'auth_token' => auth()->user()->twilio_auth_token,
                        'account_sid' => auth()->user()->twilio_account_sid,
                        'alphanumeric_sender' => auth()->user()->twilio_from,
                    ]
                )
            );
        });
    }
}
