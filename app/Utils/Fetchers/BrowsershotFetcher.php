<?php

namespace App\Utils\Fetchers;

use Spatie\Browsershot\Browsershot;

class BrowsershotFetcher extends HtmlFetcher
{
    const NAME = HtmlFetcher::CLIENT_BROWERSHOT;

    public function fetchHtml(string $url): string
    {
        return Browsershot::url($url)
        ->setNodeBinary(config('pcn.browsershot.node_bin'))
        ->setNpmBinary(config('pcn.browsershot.npm_bin'))
        ->userAgent(config('pcn.fetcher.user_agent'))
        ->disableImages()
        ->dismissDialogs()
        ->noSandbox()
        ->waitUntilNetworkIdle()
        ->bodyHtml();
    }
}
