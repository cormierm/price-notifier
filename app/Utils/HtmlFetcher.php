<?php

namespace App\Utils;

use GuzzleHttp\ClientInterface;
use Spatie\Browsershot\Browsershot;

class HtmlFetcher
{
    const CLIENT_BROWERSHOT = 'browsershot';
    const CLIENT_CURL = 'curl';
    const CLIENT_GUZZLE = 'guzzle';

    const CLIENTS = [
        self::CLIENT_BROWERSHOT,
        self::CLIENT_CURL,
        self::CLIENT_GUZZLE
    ];

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getHtmlFromUrl(string $url, string $client = self::CLIENT_BROWERSHOT)
    {
        if ($client === self::CLIENT_GUZZLE) {
            return $this->getHtmlWithGuzzle($url);
        } elseif ($client === self::CLIENT_CURL) {
            return $this->getHtmlWithCurl($url);
        }

        return $this->getHtmlWithBrowserShot($url);
    }

    public function getHtmlWithBrowserShot(string $url): string
    {
        return Browsershot::url($url)
            ->setNodeBinary(config('pcn.browsershot.node_bin'))
            ->setNpmBinary(config('pcn.browsershot.npm_bin'))
            ->userAgent(config('pcn.fetcher.user_agent'))
            ->disableImages()
            ->dismissDialogs()
            ->waitUntilNetworkIdle()
            ->bodyHtml();
    }

    public function getHtmlWithCurl(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, config('pcn.fetcher.user_agent'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, config('pcn.fetcher.timeout'));
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function getHtmlWithGuzzle(string $url): string
    {
        return (string)$this->client->request('GET', $url, [
            'headers' => [
                'User-Agent' => config('pcn.fetcher.user_agent'),
            ]
        ])->getBody();
    }
}
