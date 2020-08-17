<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\CheckRequest;
use App\Utils\HtmlFetcher;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
use Exception;
use Illuminate\Http\JsonResponse;

class Check extends Controller
{
    public function __invoke(CheckRequest $request): JsonResponse
    {
        /** @var HtmlFetcher $fetcher */
        $fetcher = resolve(HtmlFetcher::class);

        try {
            $html = $fetcher->getHtmlFromUrl($request->input('url', ''));

            $parser = new HtmlParser($html);
            $rawValue = $parser->nodeValueByXPathQuery($request->input('xpath_value', ''));
            $formattedValue = PriceHelper::numbersFromText($rawValue);

            return new JsonResponse([
                'value' => $formattedValue,
                'raw_value' => $rawValue,
                'title' =>  $request->input('xpath_name')
                    ? $parser->nodeValueByXPathQuery($request->input('xpath_name'))
                    : '',
            ]);
        } catch(Exception $e) {

            return new JsonResponse([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
