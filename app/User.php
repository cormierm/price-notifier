<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pushover_user_key',
        'pushover_api_token',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(Watcher::class);
    }

    public function priceChanges(): HasManyThrough
    {
        return $this->hasManyThrough(PriceChange::class, Watcher::class);
    }

    public function stockChanges(): HasManyThrough
    {
        return $this->hasManyThrough(StockChange::class, Watcher::class);
    }
}
