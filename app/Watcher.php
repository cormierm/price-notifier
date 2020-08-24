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
    ];

    protected $casts = [
        'user_id' => 'int',
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
}
