<?php

namespace App\Notifications;

use App\Watcher;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class StockAlert extends Notification
{
    use Queueable;

    /**
     * @var Watcher
     */
    public $watcher;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Watcher $watcher)
    {
        $this->watcher = $watcher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return array_merge([
            PushoverChannel::class,
        ], $notifiable->phone_number ? [TwilioChannel::class] : []);
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content("Stick Alert! {$this->watcher->name} @ " . $this->watcher->priceChanges()->latest()->first()->price);
    }

    public function toPushover($notifiable)
    {
        return PushoverMessage::create($this->watcher->name . "\n" . $this->watcher->priceChanges()->latest()->first()->price)
                              ->title('Stock Alert!')
                              ->sound('cashregister')
                              ->url($this->watcher->url);
    }
}
