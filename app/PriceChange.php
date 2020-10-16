<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
