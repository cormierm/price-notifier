<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'price_query',
        'price_query_type',
        'stock_query',
        'stock_query_type',
        'stock_text',
        'stock_condition',
        'client',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
