<?php

namespace App\Utils;

use DOMDocument;
use DOMNodeList;
use DOMXPath;

class HtmlParser
{
    const QUERY_TYPE_REGEX = 'regex';
    const QUERY_TYPE_XPATH = 'xpath';

    private $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function queryHtml($query, $type): string
    {
        if (!$query) {
            return '';
        }

        if ($type === self::QUERY_TYPE_REGEX) {
            return $this->regexMatch($query);
        }

        return $this->nodeValueByXPathQuery($query);
    }

    public function regexMatch(string $regex): string
    {
        preg_match($regex, $this->html, $matches);

        return $matches ? $matches[1] : '';
    }

    public function nodeValueByXPathQuery(string $query): string
    {
        $nodes = $this->nodesByXPathQuery($query);
        return $this->nodeValueFromNodes($nodes);
    }

    public function nodeHtmlByXPathQuery(string $query): string
    {
        $nodes = $this->nodesByXPathQuery($query);
        return $this->nodeHtml($nodes);
    }

    private function nodesByXPathQuery(string $query): DOMNodeList
    {
        $doc = new DOMDocument();
        @$doc->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', "UTF-8"));

        /** @var DOMXPath $xpath */
        $xpath = new DOMXpath($doc);

        return $xpath->query($query);
    }

    public function nodeValueFromNodes(DOMNodeList $nodes, int $nodeIndex = 0)
    {
        return $nodes->count() ? $nodes->item($nodeIndex)->nodeValue : '';
    }

    public function nodeHtml(DOMNodeList $nodes, int $nodeIndex = 0)
    {
        return $nodes->count() ? $nodes->item($nodeIndex)->C14N() : '';
    }
}
