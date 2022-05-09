<?php

namespace App\Utils\Fetchers;

use Illuminate\Support\Facades\Http;

class PuppeteerFetcher extends HtmlFetcher
{
    const NAME = HtmlFetcher::CLIENT_PUPPETEER;

    public function fetchHtml(string $url, string $userAgent = null): string
    {
        $response = Http::post(config('pcn.fetcher.puppeteer_host'), [
            'url' => $url,
            'user_agent' => $userAgent
        ]);

        if ($response->status() === 500) {
            throw new \Exception($response->body());
        }

        return $response->body();
    }
}
