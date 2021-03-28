<?php

namespace App\Notifications;

use App\Watcher;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;

class PriceAlert extends Notification
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
        return [PushoverChannel::class];
    }

    public function toPushover($notifiable)
    {
        return PushoverMessage::create($this->watcher->name . "\n" . number_format($this->watcher->priceChanges()->latest()->first()->price, 2))
                              ->title('Price Alert!')
                              ->sound('cashregister')
                              ->url($this->watcher->url);
    }
}
