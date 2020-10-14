<?php

namespace App\Jobs;

use GuzzleHttp\ClientInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSlackMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $message;
    /**
     * @var string
     */

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function handle(ClientInterface $client): void
    {
        $client->request('POST', 'https://slack.com/api/chat.postMessage', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('SLACK_TOKEN')
            ],
            'json' => [
                'channel' => env('SLACK_CHANNEL'),
                'text' => $this->message,
            ]
        ]);
    }
}
