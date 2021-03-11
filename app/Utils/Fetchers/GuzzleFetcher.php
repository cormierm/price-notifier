<?php

namespace App\Utils\Fetchers;

use GuzzleHttp\ClientInterface;

class GuzzleFetcher extends HtmlFetcher
{
    const NAME = HtmlFetcher::CLIENT_GUZZLE;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchHtml(string $url, string $userAgent = null): string
    {
        return (string)$this->client->request('GET', $url, [
            'headers' => [
                'User-Agent' => $userAgent ?? config('pcn.fetcher.user_agent'),
            ]
        ])->getBody();
    }
}
