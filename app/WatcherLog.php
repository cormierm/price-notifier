<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatcherLog extends Model
{
    protected $fillable = [
        'watcher_id',
        'formatted_value',
        'raw_value',
        'duration',
        'region',
        'error',
        'has_stock',
    ];

    public function watcher(): BelongsTo
    {
        return $this->belongsTo(Watcher::class);
    }
}
