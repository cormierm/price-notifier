<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\CheckRequest;
use App\Utils\Fetchers\HtmlFetcherFactory;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
use App\Watcher;
use Exception;
use Illuminate\Http\JsonResponse;

class Check extends Controller
{
    public function __invoke(CheckRequest $request): JsonResponse
    {
        $fetcher = (new HtmlFetcherFactory)->build($request->input('client'));

        try {
            $html = $fetcher->fetchHtml($request->input('url', ''), $request->user()->user_agent);

            $parser = new HtmlParser($html);
            $rawValue = $parser->nodeValueByXPathQuery($request->input('xpath_value', ''));
            $formattedValue = PriceHelper::numbersFromText($rawValue);

            if ($request->input('xpath_stock') && $request->input('stock_text')) {
                $rawStockValue = in_array(
                    $request->input('stock_condition'),
                    [Watcher::STOCK_CONDITION_CONTAINS_TEXT, Watcher::STOCK_CONDITION_MISSING_TEXT]
                )
                    ? $parser->nodeValueByXPathQuery($request->input('xpath_stock', ''))
                    : $parser->nodeHtmlByXPathQuery($request->input('xpath_stock', ''));

                $contains = in_array(
                    $request->input('stock_condition'),
                    [Watcher::STOCK_CONDITION_CONTAINS_TEXT, Watcher::STOCK_CONDITION_CONTAINS_HTML]
                );

                $hasStock = ($contains && stripos($rawStockValue, $request->input('stock_text')) !== false) ||
                    (!$contains && stripos($rawStockValue, $request->input('stock_text')) === false);
            }

            return new JsonResponse([
                'value' => $formattedValue,
                'title' => $parser->nodeValueByXPathQuery('//title'),
                'has_stock' => $hasStock ?? null,
                'debug' => [
                    'value_inner_text' => $rawValue,
                    'stock_inner_text' => $request->input('xpath_stock') ? $parser->nodeValueByXPathQuery($request->input('xpath_stock', '')) : '',
                    'stock_html' => $request->input('xpath_stock') ? $parser->nodeHtmlByXPathQuery($request->input('xpath_stock', '')) : '',
                ]
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
