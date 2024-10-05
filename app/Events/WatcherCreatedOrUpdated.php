<?php

namespace App\Events;

use App\Http\Resources\WatcherResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WatcherCreatedOrUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $watcher;

    public function __construct(WatcherResource $watcher)
    {
        $this->watcher = $watcher;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("user.{$this->watcher->user->id}.watchers");
    }

    public function broadcastAs()
    {
        return 'update';
    }
}
