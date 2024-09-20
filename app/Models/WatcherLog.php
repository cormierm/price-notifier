<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatcherLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'watcher_id',
        'formatted_value',
        'raw_value',
        'duration',
        'region',
        'error',
        'has_stock',
        'raw_stock',
    ];

    public function watcher(): BelongsTo
    {
        return $this->belongsTo(Watcher::class);
    }
}
