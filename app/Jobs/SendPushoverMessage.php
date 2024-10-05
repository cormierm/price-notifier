<?php

namespace App\Jobs;

use App\Models\User;
use GuzzleHttp\ClientInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushoverMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $url;

    public function __construct(User $user, string $title, string $message, string $url)
    {
        $this->user = $user;
        $this->message = $message;
        $this->title = $title;
        $this->url = $url;
    }

    public function handle(ClientInterface $client): void
    {
        $client->request('POST', 'https://api.pushover.net/1/messages.json', [
            'form_params' => [
                'token' => $this->user->pushover_api_token,
                'user' => $this->user->pushover_user_key,
                'title' => $this->title,
                'message' => $this->message,
                'url' => $this->url,
                'sound' => 'cashregister'
            ]
        ]);
    }
}
