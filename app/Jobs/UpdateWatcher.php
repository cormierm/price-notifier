<?php

namespace App\Jobs;

use App\Events\WatcherCreatedOrUpdated;
use App\Http\Resources\WatcherResource;
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
        $hasStock = null;

        /** @var HtmlFetcher $fetcher */
        $fetcher = resolve(HtmlFetcher::class);

        try {
            $html = $fetcher->getHtmlFromUrl($this->watcher->url, $this->watcher->client);

            $parser = new HtmlParser($html);
            $rawValue = $parser->nodeValueByXPathQuery($this->watcher->query);
            $formattedValue = PriceHelper::numbersFromText($rawValue);

            if ($this->watcher->xpath_stock && $this->watcher->stock_text) {
                $rawStockValue = $parser->nodeValueByXPathQuery($this->watcher->xpath_stock);
                $hasStock = strpos($rawStockValue, $this->watcher->stock_text) !== false;

                if ($this->watcher->stock_alert && $this->watcher->has_stock === false && $hasStock) {
                    SendPushoverMessage::dispatch(
                        $this->watcher->user,
                        'Stock Alert!',
                        "{$this->watcher->name}\n\${$formattedValue}",
                        $this->watcher->url
                    );
                }

                $this->watcher->update([
                    'has_stock' => $hasStock,
                ]);
            }


            if ($formattedValue) {
                $this->sendAlerts($formattedValue);

                $this->watcher->update([
                    'value' => $formattedValue,
                ]);

                $this->setLowestPrice($formattedValue);

                if (!$this->watcher->initial_value) {
                    $this->watcher->update([
                        'initial_value' => $formattedValue,
                    ]);
                }
            } else {
                $this->error = 'Formatted value was empty. Found node value: ' . $rawValue;
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        $this->watcher->update([
            'last_sync' => Carbon::now(),
        ]);

        event(new WatcherCreatedOrUpdated(WatcherResource::make($this->watcher)));

        WatcherLog::create([
            'watcher_id' => $this->watcher->id,
            'formatted_value' => $formattedValue ?? null,
            'raw_value' => $rawValue ?? null,
            'duration' => Carbon::now()->diffInMilliseconds($startTime),
            'region' => config('pcn.region'),
            'error' => $this->error,
            'has_stock' => $hasStock,
        ]);
    }

    private function sendAlerts(string $formattedValue)
    {
        $alertPrice = number_format($this->watcher->alert_value, 2);
        $oldPrice = number_format($this->watcher->value, 2);
        $newPrice = number_format($formattedValue, 2);

        if ($alertPrice && $newPrice && $oldPrice !== $newPrice && $newPrice < $alertPrice) {
            SendPushoverMessage::dispatch(
                $this->watcher->user,
                'Price Alert!',
                "{$this->watcher->name}\n\${$newPrice}",
                $this->watcher->url
            );
        }
    }

    private function setLowestPrice(string $formattedValue)
    {
        $price = number_format($formattedValue, 2);
        $lowestPrice = number_format($this->watcher->lowest_price, 2);

        if (!$this->watcher->lowest_price || $price <= $lowestPrice) {
            $this->watcher->update([
                'lowest_price' => $formattedValue,
                'lowest_at' => Carbon::now()
            ]);
        }
    }
}
