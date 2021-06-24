<?php

namespace App\Http\Controllers\Api\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\StoreRequest;
use App\Utils\Fetchers\HtmlFetcher;
use App\Utils\Fetchers\HtmlFetcherFactory;
use App\Utils\HtmlParser;
use Exception;
use Illuminate\Http\JsonResponse;

class Store extends Controller
{
    public function __invoke(StoreRequest $request): JsonResponse
    {
        $request->user()->watchers()->create([
            'user_id' => $request->user()->id,
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'price_query' => $request->input('price_query'),
            'xpath_stock' => $request->input('xpath_stock'),
            'stock_condition' => $request->input('stock_condition'),
            'stock_text' => $request->input('stock_text'),
            'client' => $request->input('client'),
            'stock_alert' => false,
            'interval_id' => $request->input('interval_id'),
        ]);

        return response()->json();
    }

    private function getPageTitle($url): string
    {
        $fetcher = (new HtmlFetcherFactory)->build(HtmlFetcher::CLIENT_BROWERSHOT);

        try {
            $html = $fetcher->fetchHtml($url);
            $parser = new HtmlParser($html);

            return mb_strimwidth($parser->nodeValueByXPathQuery('//title'), 0, 50, "...");
        } catch (Exception $e) {
            return '';
        }
    }
}
