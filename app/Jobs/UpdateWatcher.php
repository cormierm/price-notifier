<?php

namespace App\Jobs;

use App\Events\WatcherCreatedOrUpdated;
use App\Http\Resources\WatcherResource;
use App\Models\User;
use App\Models\Watcher;
use App\Models\WatcherLog;
use App\Utils\Fetchers\HtmlFetcherFactory;
use App\Utils\HtmlParser;
use App\Utils\PriceHelper;
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
    private $price = null;
    private $rawPrice = null;
    private $rawStock = null;
    private $hasStock = null;

    public function __construct(Watcher $watcher)
    {
        $this->watcher = $watcher;
    }

    public function handle(): void
    {
        $startTime = Carbon::now();
        $fetcher = (new HtmlFetcherFactory)->build($this->watcher->client);

        try {
            $html = $fetcher->fetchHtml($this->watcher->url, $this->watcher->user->user_agent);
            $parser = new HtmlParser($html);

            $this->rawPrice = $parser->queryInnerHtml($this->watcher->price_query, $this->watcher->price_query_type);
            $this->price = PriceHelper::numbersFromText($this->rawPrice);

            $canUpdateStock = !$this->watcher->stock_requires_price || ($this->watcher->stock_requires_price && $this->price);
            $this->hasStock = $canUpdateStock ? $this->calculateStock($parser) : null;

            if ($this->price) {
                $this->setLowestPrice($this->price);

                if (!$this->watcher->initial_value) {
                    $this->watcher->update([
                        'initial_value' => $this->price,
                    ]);
                }
            }

            $this->updatePriceChanges();
            $this->updateStockChanges();

            $this->sendAlerts();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        if ($this->hasStock === null) {
            $this->watcher->update([
                'last_sync' => Carbon::now(),
                'value' => $this->price,
            ]);
        } else {
            $this->watcher->update([
                'has_stock' => $this->hasStock,
                'last_sync' => Carbon::now(),
                'value' => $this->price,
            ]);
        }

        event(new WatcherCreatedOrUpdated(WatcherResource::make($this->watcher)));

        $this->rawPrice = strlen($this->rawPrice) > 255
            ? substr($this->rawPrice, 0, 190)
            : $this->rawPrice;

        WatcherLog::create([
            'watcher_id' => $this->watcher->id,
            'formatted_value' => $this->price ?? null,
            'raw_value' => $this->rawPrice ?? null,
            'duration' => Carbon::now()->diffInMilliseconds($startTime, true),
            'region' => config('pcn.region'),
            'error' => strlen($this->error) > config('pcn.fetcher.error_max_length')
                ? substr($this->error, 0, config('pcn.fetcher.error_max_length') - 1)
                : $this->error,
            'has_stock' => $this->hasStock,
            'raw_stock' => $this->rawStock,
        ]);
    }

    private function sendAlerts()
    {
        if ($this->price && $this->watcher->value) {
            $alertPrice = number_format($this->watcher->alert_value, 2, '.', '');
            $oldPrice = number_format($this->watcher->value, 2, '.', '');
            $newPrice = number_format($this->price, 2, '.', '');

            if (
                $alertPrice && $newPrice && $oldPrice !== $newPrice
                && (
                    ($this->watcher->alert_condition === Watcher::ALERT_CONDITION_LESS_THAN && $this->price < $this->watcher->alert_value)
                    || ($this->watcher->alert_condition === Watcher::ALERT_CONDITION_GREATER_THAN && $this->price > $this->watcher->alert_value)
                )
            ) {
                SendPushoverMessage::dispatch(
                    $this->watcher->user,
                    'Price Alert!',
                    "{$this->watcher->name}\n\${$newPrice}",
                    $this->watcher->url
                );
            }
        }

        if ($this->hasStock && $this->watcher->stock_alert && $this->watcher->has_stock === false) {
            if (env('NOTIFY_ALL_USERS')) {
                foreach (User::all() as $user) {
                    SendPushoverMessage::dispatch(
                        $user,
                        'Stock Alert!',
                        "{$this->watcher->name}\n\${$this->price}",
                        $this->watcher->url
                    );
                }
            } else {
                SendPushoverMessage::dispatch(
                    $this->watcher->user,
                    'Stock Alert!',
                    "{$this->watcher->name}\n\${$this->price}",
                    $this->watcher->url
                );
            }

//            if (env('SLACK_CHANNEL', false) && env('SLACK_TOKEN', false)) {
//                SendSlackMessage::dispatch("Stock Alert!\n{$this->watcher->name}\n\${$this->price}\n{$this->watcher->url}");
//            }
        }
    }

    private function setLowestPrice(string $formattedValue)
    {
        $price = number_format($formattedValue, 2, '.', '');
        $lowestPrice = number_format($this->watcher->lowest_price, 2, '.', '');

        if (!$this->watcher->lowest_price || $price <= $lowestPrice) {
            $this->watcher->update([
                'lowest_price' => $formattedValue,
                'lowest_at' => Carbon::now()
            ]);
        }
    }

    private function updatePriceChanges()
    {
        if (!$this->price) {
            return;
        }

        $lastPriceChange = $this->watcher->priceChanges()->latest()->first();
        if (!$lastPriceChange
            || $lastPriceChange->price !== $this->price
        ) {
            $this->watcher->priceChanges()->create([
                'price' => $this->price,
            ]);
        }
    }

    private function updateStockChanges()
    {
        if ($this->hasStock === null) {
            return;
        }

        $lastStockChange = $this->watcher->stockChanges()->latest()->first();

        if (!$lastStockChange
            || $lastStockChange->stock !== $this->hasStock
        ) {
            $this->watcher->stockChanges()->create([
                'stock' => $this->hasStock,
            ]);
        }
    }

    private function calculateStock(HtmlParser $parser)
    {
        if ($this->watcher->stock_query && $this->watcher->stock_query_type && $this->watcher->stock_text) {
            $this->rawStock = in_array(
                $this->watcher->stock_condition,
                [Watcher::STOCK_CONDITION_CONTAINS_TEXT, Watcher::STOCK_CONDITION_MISSING_TEXT]
            )
                ? $parser->queryInnerHtml($this->watcher->stock_query, $this->watcher->stock_query_type)
                : $parser->queryOuterHtml($this->watcher->stock_query, $this->watcher->stock_query_type);

            $containText = in_array(
                $this->watcher->stock_condition,
                [Watcher::STOCK_CONDITION_CONTAINS_TEXT, Watcher::STOCK_CONDITION_CONTAINS_HTML]
            );

            $hasStock = ($containText && stripos($this->rawStock, $this->watcher->stock_text) !== false) ||
                (!$containText && stripos($this->rawStock, $this->watcher->stock_text) === false);

            $this->rawStock = strlen($this->rawStock) > 255
                ? substr($this->rawStock, 0, 190)
                : $this->rawStock;

            return $hasStock;
        }

        return null;
    }
}
