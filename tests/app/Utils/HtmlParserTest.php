<?php

namespace Tests\App\Utils;

use App\Utils\HtmlParser;
use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class HtmlParserTest extends TestCase
{

    /** @test */
    public function itReturnsEmptyStringForNotFoundQuery(): void
    {
        $html = '<html><body><div></div></body></html>';

        $parser = new HtmlParser($html);

        $this->assertEquals('', $parser->nodeValueByXPathQuery('not-found-class'));
    }


    /** @test */
    public function itCanGetTextByQueryFromUrl(): void
    {
        $html = '<html><body><div id="pull-right-price" class="pull-right "><span class="value">149.99</span><span class="currency">$</span></div></div></body></html>';

        $parser = new HtmlParser($html);

        $this->assertEquals('149.99', $parser->nodeValueByXPathQuery('//div[@id="pull-right-price"]/span[@class="value"]'));
    }

    /** @test */
    public function itCanGetHtmlFromXpathQuery(): void
    {
        $html = '<html><body><div id="test-div"><input class="hello"><span>Hi</span></div></div></body></html>';

        $parser = new HtmlParser($html);

        $this->assertEquals('<div id="test-div"><input class="hello"></input><span>Hi</span></div>', $parser->nodeHtmlByXPathQuery('//div[@id="test-div"]'));
    }
}
