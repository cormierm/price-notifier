<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\CheckRequest;
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
            $html = $fetcher->fetchHtml($request->input('url', ''));

            $parser = new HtmlParser($html);
            $rawValue = $parser->nodeValueByXPathQuery($request->input('xpath_value', ''));
            $formattedValue = PriceHelper::numbersFromText($rawValue);

            if ($request->input('xpath_stock') && $request->input('stock_text')) {
                $rawStockValue = $parser->nodeValueByXPathQuery($request->input('xpath_stock', ''));
                $hasStock = ($request->input('stock_contains') && stripos($rawStockValue, $request->input('stock_text')) !== false) ||
                    (!$request->input('stock_contains', false) && stripos($rawStockValue, $request->input('stock_text')) === false);
            }

            return new JsonResponse([
                'value' => $formattedValue,
                'raw_value' => $rawValue,
                'title' =>  $parser->nodeValueByXPathQuery('//title'),
                'raw_stock_value' => $rawStockValue ?? null,
                'has_stock' => $hasStock ?? null,
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
