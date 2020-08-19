<?php

namespace App\Policies;

use App\User;
use App\Watcher;
use Illuminate\Auth\Access\HandlesAuthorization;

class WatcherPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Watcher $watcher): bool
    {

        return $watcher->user_id === $user->id;
    }

    public function view(User $user, Watcher $watcher): bool
    {

        return $watcher->user_id === $user->id;
    }
}
