<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatcherLog extends Model
{
    protected $fillable = [
        'formatted_value',
        'raw_value',
        'status_code',
        'duration',
        'error',
    ];
}
