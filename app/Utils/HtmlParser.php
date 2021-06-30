<?php

namespace App\Utils;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use PhpQuery\PhpQuery;

class HtmlParser
{
    const QUERY_TYPE_REGEX = 'regex';
    const QUERY_TYPE_SELECTOR = 'selector';
    const QUERY_TYPE_XPATH = 'xpath';
    const QUERY_TYPES = [
        self::QUERY_TYPE_REGEX,
        self::QUERY_TYPE_SELECTOR,
        self::QUERY_TYPE_XPATH
    ];

    private $html;

    public function __construct(string $html)
    {
        $this->html = $html;
    }

    public function queryInnerHtml(?string $query, ?string $type): string
    {
        if (!in_array($type, self::QUERY_TYPES)) {
            return '';
        }

        if ($type === self::QUERY_TYPE_REGEX) {
            return $this->regexMatch($query);
        }

        if ($type === self::QUERY_TYPE_SELECTOR) {
            return $this->querySelector($query, 'inner');
        }

        return $this->nodeValueByXPathQuery($query);
    }

    public function queryOuterHtml(?string $query, ?string $type): string
    {
        if (!in_array($type, self::QUERY_TYPES)) {
            return '';
        }

        if ($type === self::QUERY_TYPE_REGEX) {
            return $this->regexMatch($query);
        }

        if ($type === self::QUERY_TYPE_SELECTOR) {
            return $this->querySelector($query, 'outer');
        }

        return $this->nodeHtmlByXPathQuery($query);
    }

    public function querySelector(string $selector, $htmlType = 'inner'): string
    {
        $pq = new PhpQuery;
        $pq->load_str($this->html);

        if ($htmlType === 'inner') {
            return $pq->innerHTML($pq->query($selector)[0]);
        }

        return $pq->outerHTML($pq->query($selector)[0]);
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
