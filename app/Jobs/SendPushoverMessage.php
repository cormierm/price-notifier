<?php

namespace App\Jobs;

use App\User;
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

    public function __construct(User $user, string $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function handle(ClientInterface $client): void
    {
        $client->request('POST', 'https://api.pushover.net/1/messages.json', [
            'form_params' => [
                'token' => $this->user->pushover_api_token,
                'user' => $this->user->pushover_user_key,
                'message' => $this->message,
            ]
        ]);
    }
}
