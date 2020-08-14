<?php

namespace Tests\App\Utils;

use App\Utils\HtmlFetcher;
use Tests\TestCase;

class HtmlFetcherTest extends TestCase
{
    /** @test */
    public function itCanGetHtmlFromCT(): void
    {
        $this->markTestSkipped();
        $url = 'https://www.canadiantire.ca/en/pdp/dyson-cyclone-v10-motorhead-cordless-vacuum-0438161p.html#srp';
        $client = new HtmlFetcher;
        dd($client->getHtmlFromUrl($url));
    }
}
