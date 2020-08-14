<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Watcher extends Model
{
    protected $fillable = [
        'name',
        'url',
        'user_id',
        'query',
        'last_sync',
        'initial_value',
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

    public function interval(): HasOne
    {
        return $this->hasOne(Interval::class);
    }
}
