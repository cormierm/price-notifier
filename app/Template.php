<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'domain',
        'xpath_name',
        'xpath_value',
    ];
}
