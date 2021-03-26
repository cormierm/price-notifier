<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'domain',
        'xpath_value',
        'client',
        'xpath_stock',
        'stock_text',
        'stock_condition',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
