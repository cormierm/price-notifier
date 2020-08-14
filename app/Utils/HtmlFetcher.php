<?php

namespace App\Utils;

use GuzzleHttp\ClientInterface;
use Spatie\Browsershot\Browsershot;

class HtmlFetcher
{
    public function getHtmlFromUrl(string $url)
    {
        return $this->getHtmlWithBrowserShot($url);
    }

    public function getHtmlWithBrowserShot(string $url): string
    {
        return Browsershot::url($url)
            ->setNodeBinary(config('pcn.browsershot.node_bin'))
            ->setNpmBinary(config('pcn.browsershot.npm_bin'))
            ->userAgent(config('pcn.fetcher.user_agent'))
            ->disableImages()
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

    public function getHtmlWithGuzzle(string $url, ClientInterface $client): string
    {
        return (string)$client->request('GET', $url, [
            'headers' => [
                'User-Agent' => config('pcn.fetcher.user_agent'),
            ]
        ])->getBody();
    }
}
