<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockChange extends Model
{
    protected $fillable = [
        'watcher_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'boolean',
    ];
}
