<?php

namespace App\Jobs;

use App\Utils\HtmlFetcher;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
use App\Watcher;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Watcher
     */
    private $watcher;

    public function __construct(Watcher $watcher)
    {
        $this->watcher = $watcher;
    }

    public function handle(): void
    {
        /** @var HtmlFetcher $fetcher */
        $fetcher = resolve(HtmlFetcher::class);

        $html = $fetcher->getHtmlFromUrl($this->watcher->url);

        if ($this->watcher->query_type === 'class') {
            $query = '//*[@class="' . $this->watcher->query . '"]';
        } elseif ($this->watcher->query_type === 'id') {
            $query = '//*[@id="' . $this->watcher->query . '"]';
        } else {
            $query = $this->watcher->query;
        }

        $parser = new HtmlParser($html);
        $text = $parser->nodeValueByXPathQuery($query);

        $this->watcher->update([
            'last_sync' => Carbon::now(),
            'value' => PriceHelper::numbersFromText($text),
        ]);
    }
}
