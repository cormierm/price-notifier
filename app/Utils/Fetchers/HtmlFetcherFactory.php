<?php

namespace App\Utils\Fetchers;

class HtmlFetcherFactory
{
    private $fetchers = [
        BrowsershotFetcher::class,
        CurlFetcher::class,
        GuzzleFetcher::class,
    ];

    public function build(string $client): HtmlFetcher
    {
        foreach ($this->fetchers as $fetcher) {
            if ($fetcher::satisfiedBy($client)) {
                return resolve($fetcher);
            }
        }

        return resolve(BrowsershotFetcher::class);
    }
}
