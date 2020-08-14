<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Utils\HtmlFetcher;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class Sync extends Controller
{
    public function __invoke(Watcher $watcher, HtmlFetcher $fetcher)
    {
        $html = $fetcher->getHtmlFromUrl($watcher->url);

        if ($watcher->query_type === 'class') {
            $query = '//*[@class="' . $watcher->query . '"]';
        } elseif ($watcher->query_type === 'id') {
            $query = '//*[@id="' . $watcher->query . '"]';
        } else {
            $query = $watcher->query;
        }

        $parser = new HtmlParser($html);
        $text = $parser->nodeValueByXPathQuery($query);

        $watcher->update([
            'last_sync' => Carbon::now(),
            'value' => PriceHelper::numbersFromText($text),
        ]);

        return new JsonResponse([
            'watcher' => $watcher
        ]);
    }
}
