<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\CheckRequest;
use App\Models\Watcher;
use App\Utils\Fetchers\HtmlFetcherFactory;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
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

            $rawValue = $parser->queryInnerHtml($request->input('price_query'), $request->input('price_query_type'));
            $formattedValue = PriceHelper::numbersFromText($rawValue);

            $canUpdateStock = !$request->input('stock_requires_price') || ($request->input('stock_requires_price') && $formattedValue);
            if ($canUpdateStock && $request->input('stock_query') && $request->input('stock_query_type') && $request->input('stock_text')) {
                $rawStockValue = in_array(
                    $request->input('stock_condition'),
                    [Watcher::STOCK_CONDITION_CONTAINS_TEXT, Watcher::STOCK_CONDITION_MISSING_TEXT]
                )
                    ? $parser->queryInnerHtml($request->input('stock_query', ''), $request->input('stock_query_type', ''))
                    : $parser->queryOuterHtml($request->input('stock_query', ''), $request->input('stock_query_type', ''));

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
                    'stock_inner_text' => $request->input('stock_query')
                        ?  $parser->queryInnerHtml($request->input('stock_query', ''), $request->input('stock_query_type', ''))
                        : '',
                    'stock_outer_html' => $request->input('stock_query')
                        ? $parser->queryOuterHtml($request->input('stock_query', ''), $request->input('stock_query_type', ''))
                        : '',
                ]
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
