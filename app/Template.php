<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'domain',
        'price_query',
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
