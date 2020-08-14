<?php

namespace Tests\Src;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Src\PriceFetcher;
use Tests\TestCase;

class PriceFetcherTest extends TestCase
{
    /** @test */
    public function itCanGetTextByIdFromUrl(): void
    {
        $text = 'CDN$ 123.34';
        $this->bindMockGuzzleClient([
            new Response(200, [], '<html><body><div><span id="foo-bar">' . $text . '</span></div></body></html>'),
        ]);

        $client = resolve(PriceFetcher::class);

        $client->loadHtmlByUrl('https://www.some-page.com/with/a/price');

        $this->assertEquals($text, $client->getInnerTextById('foo-bar'));
    }

    /** @test */
    public function itCanGetTextByClassFromUrl(): void
    {
        $text = 'CDN$ 123.34';
        $this->bindMockGuzzleClient([
            new Response(200, [], '<html><body><div><span class="foo-bar">' . $text . '</span></div></body></html>'),
        ]);

        $client = resolve(PriceFetcher::class);

        $client->loadHtmlByUrl('https://www.some-page.com/with/a/price');

        $this->assertEquals($text, $client->getInnerTextByClass('foo-bar'));
    }

    /** @test */
    public function itReturnsEmptyStringForNotFoundQuery(): void
    {
        $this->bindMockGuzzleClient([
            new Response(200, [], '<html><body><div></div></body></html>'),
        ]);

        $client = resolve(PriceFetcher::class);

        $client->loadHtmlByUrl('https://www.some-page.com/without/a/price');

        $this->assertEquals('', $client->getInnerTextByClass('not-found-class'));
    }

    /** @test */
    public function itCanParsePriceFromText(): void
    {
        $text = 'CDN$ 23,432,4343.334';

        $client = new PriceFetcher(new Client);

        $this->assertEquals(234324343.33, $client->getPriceFromText($text));
    }

    private function bindMockGuzzleClient(array $responses = [])
    {
        $mock = new MockHandler($responses);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->bind(ClientInterface::class, function () use ($client) {
            return $client;
        });
    }

    /** @test */
    public function itCanGetTextByQueryFromUrl(): void
    {
        $this->bindMockGuzzleClient([
            new Response(200, [], '<html><body><div id="pull-right-price" class="pull-right "><span class="value">149.99</span><span class="currency">$</span></div></div></body></html>'),
        ]);

        $client = resolve(PriceFetcher::class);

        $client->loadHtmlByUrl('https://www.some-page.com/with/a/price');

        $this->assertEquals('149.99', $client->getInnerTextByXPathQuery('//div[@id="pull-right-price"]/span[@class="value"]'));
    }

    /** @test */
    public function itTestingGround(): void
    {
//        $this->markTestSkipped();
        // ct
        $url = 'https://www.canadiantire.ca/en/pdp/dyson-cyclone-v10-motorhead-cordless-vacuum-0438161p.html#srp';
        $client = resolve(PriceFetcher::class);
//        dd($client->getHtmlWithBrowserShot($url));

        $client->loadHtmlByUrl($url);
//        dd($client->getInnerTextByXPathQuery('//div[@id="pull-right-price"]/span[@class="value"]'));
        dd($client->getInnerTextByClass('price__reg-value'));
//
//        $url = 'https://www.amazon.ca/gp/product/B07TKDPKVF?pf_rd_r=VWQKSYN6K3D4JF8318H6&pf_rd_p=05326fd5-c43e-4948-99b1-a65b129fdd73';
//        $url = 'https://www.costco.ca/arcan-3-ton-professional-grade-hybrid-service-jack.product.100317470.html';
//        $url = 'http://www.google.ca';
//        /** @var PriceFetcher $client */
//        $client = resolve(PriceFetcher::class);
//        $client->getHtmlWithGuzzle($url));
//        dd($client->getHtmlWithCurl($url));
//        $client->loadHtmlByUrl($url);
//        dd($client->getInnerTextByXPathQuery('//div[@id="pull-right-price"]/span[@class="value"]'));
//        dd($client->getInnerTextById('priceblock_ourprice'));
    }
}
