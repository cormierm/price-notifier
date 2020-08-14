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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WatcherLog::class);
    }

    public function interval(): BelongsTo
    {
        return $this->belongsTo(Interval::class);
    }
}
