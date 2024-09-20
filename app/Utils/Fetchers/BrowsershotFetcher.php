<?php

namespace App\Utils\Fetchers;

use App\Exceptions\NoHtmlContentException;
use Spatie\Browsershot\Browsershot;

class BrowsershotFetcher extends HtmlFetcher
{
    const NAME = HtmlFetcher::CLIENT_BROWERSHOT;

    public function fetchHtml(string $url, string $userAgent = null): string
    {
        $html = Browsershot::url($url)
        ->setNodeBinary(config('pcn.browsershot.node_bin'))
        ->setNpmBinary(config('pcn.browsershot.npm_bin'))
        ->userAgent($userAgent ?? config('pcn.fetcher.user_agent'))
        ->disableImages()
        ->dismissDialogs()
        ->noSandbox()
        ->waitUntilNetworkIdle()
        ->windowSize(1920, 1080)
        ->setChromePath(config('pcn.browsershot.chrome_path'))
        ->bodyHtml();

        if (!$html) {
            throw new NoHtmlContentException('Empty HTML content');
        }

        return $html;
    }
}
