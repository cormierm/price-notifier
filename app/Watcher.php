<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watcher extends Model
{
    protected $fillable = [
        'name',
        'url',
        'user_id',
        'query_type',
        'query',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
