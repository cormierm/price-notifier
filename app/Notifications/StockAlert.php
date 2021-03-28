<?php

namespace App\Notifications;

use App\Watcher;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return [PushoverChannel::class, TwilioChannel::class];
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
