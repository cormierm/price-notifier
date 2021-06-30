<?php

namespace Tests\App\Utils;

use App\Utils\HtmlParser;
use Tests\TestCase;

class HtmlParserTest extends TestCase
{
    /** @test */
    public function itReturnsInnerHtmlFromQuerySelector(): void
    {
        $querySelector = '.value';
        $html = '<html><body><div id="pull-right-price" class="pull-right "><span class="value">149.99</span><span class="currency">$</span></div></div></body></html>';

        $parser = new HtmlParser($html);

        $this->assertEquals('149.99', $parser->queryHtml($querySelector, HtmlParser::QUERY_TYPE_SELECTOR));
    }

    /** @test */
    public function itReturnsTextFromRegexMatch(): void
    {
        $regex = '/before(.*?)after/';
        $html = '<html><body><div>before$1,800.00after</div></body></html>';
        $parser = new HtmlParser($html);

        $this->assertEquals('$1,800.00', $parser->queryHtml($regex, HtmlParser::QUERY_TYPE_REGEX));
    }

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
    public function itCanGetOuterHtmlFromXpathQuery(): void
    {
        $html = '<html><body><div id="test-div"><input class="hello"><span>Hi</span></div></div></body></html>';

        $parser = new HtmlParser($html);

        $this->assertEquals('<div id="test-div"><input class="hello"></input><span>Hi</span></div>', $parser->nodeHtmlByXPathQuery('//div[@id="test-div"]'));
    }
}
