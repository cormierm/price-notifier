<?php

namespace Src;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\ClientInterface;
use Spatie\Browsershot\Browsershot;

class PriceFetcher
{
    /**
     * @var ClientInterface
     */
    private $client;
    private $html;

    public function __construct(ClientInterface $client)
    {

        $this->client = $client;
    }

    public function loadHtmlByUrl(string $url): self
    {
        $this->html = $this->getHtmlWithBrowserShot($url);

        return $this;
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

    public function getHtmlWithGuzzle(string $url): string
    {
        return (string)$this->client->request('GET', $url, [
            'headers' => [
                'User-Agent' => config('pcn.fetcher.user_agent'),
            ]
        ])->getBody();
    }

    // //span[@class="p13n-sc-price"]
    public function getInnerTextByXPathQuery($query): string
    {
        return $this->getNodeValue($this->xpathQuery($query));
    }

    public function getInnerTextById($id): string
    {
        return $this->getNodeValue($this->xpathQuery('//*[@id="' . $id . '"]'));
    }

    public function getInnerTextByClass($class): string
    {
        return $this->getNodeValue($this->xpathQuery('//*[@class="' . $class . '"]'));
    }

    public function getPriceFromText(string $text): string
    {
        if (!$text) {
            return '';
        }

        preg_match_all(config('pcn.regex.price'), $text, $matches);

        return $matches ? str_replace(' ', '', str_replace(',', '', $matches[0][0])) : '';
    }

    private function getNodeValue($elements): string
    {
        return $elements->count() ? $elements->item(0)->nodeValue : '';
    }

    private function xpathQuery($query)
    {
        $doc = new DOMDocument();
        $searchPage = mb_convert_encoding($this->html, 'HTML-ENTITIES', "UTF-8");
        @$doc->loadHTML($searchPage);
        $xpath = new DOMXpath($doc);
        return $xpath->query($query);
    }
}
