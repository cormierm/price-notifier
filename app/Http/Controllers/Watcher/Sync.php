<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Src\PriceFetcher;

class Sync extends Controller
{
    public function __invoke(Watcher $watcher, PriceFetcher $fetcher)
    {
        $fetcher->loadHtmlByUrl($watcher->url);

        if ($watcher->query_type === 'class') {
            $text = $fetcher->getInnerTextByClass($watcher->query);
        } elseif ($watcher->query_type === 'id') {
            $text = $fetcher->getInnerTextById($watcher->query);
        } else {
            $text = $fetcher->getInnerTextByXPathQuery($watcher->query);
        }

        $watcher->update([
            'last_sync' => Carbon::now(),
            'value' => $fetcher->getPriceFromText($text),
        ]);

        return new JsonResponse([
            'watcher' => $watcher
        ]);
    }
}
