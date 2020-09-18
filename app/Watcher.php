<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Watcher extends Model
{
    protected $fillable = [
        'name',
        'url',
        'user_id',
        'query',
        'last_sync',
        'initial_value',
        'interval_id',
        'value',
        'alert_value',
        'client',
        'lowest_price',
        'lowest_at',
        'region_id',
        'xpath_stock',
        'stock_text',
        'stock_alert',
        'has_stock',
        'stock_contains',
    ];

    protected $casts = [
        'user_id' => 'int',
        'stock_alert' => 'boolean',
        'has_stock' => 'boolean',
        'stock_contains' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastLog(): ?WatcherLog
    {
        return $this->logs()->latest('created_at')->first();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WatcherLog::class);
    }

    public function interval(): BelongsTo
    {
        return $this->belongsTo(Interval::class);
    }

    public function urlDomain()
    {
        return str_replace('www.', '', parse_url($this->url, PHP_URL_HOST));
    }

    public function getStatusAttribute()
    {
        if (!$this->interval || !$this->interval->minutes) {
            return 'disabled';
        }

        $lastLog = $this->lastLog();
        if ($lastLog && $lastLog->error) {
            return 'error';
        }

        return 'ok';
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
