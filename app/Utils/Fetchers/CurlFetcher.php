<?php

namespace App\Utils\Fetchers;

class CurlFetcher extends HtmlFetcher
{
    const NAME = HtmlFetcher::CLIENT_CURL;

    public function fetchHtml(string $url): string
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
}
