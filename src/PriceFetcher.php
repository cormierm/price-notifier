<?php

namespace Src;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\ClientInterface;

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
        $this->html = (string) $this->client->get($url)->getBody();

        return $this;
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
        preg_match_all(config('pcn.regex.price'), $text, $matches);

        return $matches ? str_replace(' ', '', str_replace(',', '', $matches[0][0])) : '';
    }

    private function getNodeValue($elements): string
    {
        return $elements->count() ? $elements->item(0)->nodeValue : '';
    }

    private function xpathQuery($query)
    {
        $doc = new DOMDocument('1.0');
        $searchPage = mb_convert_encoding($this->html, 'HTML-ENTITIES', "UTF-8");
        @$doc->loadHTML($searchPage);
        $xpath = new DOMXpath($doc);
        return $xpath->query($query);
    }
}
