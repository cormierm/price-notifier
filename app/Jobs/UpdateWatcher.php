<?php

namespace App\Jobs;

use App\Utils\HtmlFetcher;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
use App\Watcher;
use App\WatcherLog;
use Carbon\Carbon;
use Exception;
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
    private $error = null;

    public function __construct(Watcher $watcher)
    {
        $this->watcher = $watcher;
    }

    public function handle(): void
    {
        $startTime = Carbon::now();

        /** @var HtmlFetcher $fetcher */
        $fetcher = resolve(HtmlFetcher::class);

        try {
            $html = $fetcher->getHtmlFromUrl($this->watcher->url);

            $parser = new HtmlParser($html);
            $rawValue = $parser->nodeValueByXPathQuery($this->watcher->query);
            $formattedValue = PriceHelper::numbersFromText($rawValue);

            if ($formattedValue) {
                $this->watcher->update([
                    'last_sync' => Carbon::now(),
                    'value' => $formattedValue,
                ]);
            } else {
                $this->error = 'Formatted value was empty. Found node value: ' . $rawValue;
            }
        } catch(Exception $e) {
            $this->error = $e->getMessage();
        }

        WatcherLog::create([
            'watcher_id' => $this->watcher->id,
            'formatted_value' => $formattedValue ?? null,
            'raw_value' => $rawValue ?? null,
            'duration' => Carbon::now()->diffInMilliseconds($startTime),
            'error' => $this->error,
        ]);
    }
}
