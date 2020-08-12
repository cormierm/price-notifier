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
}
