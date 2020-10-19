<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceChange extends Model
{
    protected $fillable = [
        'watcher_id',
        'price',
        'stock',
    ];

    protected $casts = [
        'stock' => 'boolean',
    ];

    public function watcher(): BelongsTo
    {
        return $this->belongsTo(Watcher::class);
    }
}
