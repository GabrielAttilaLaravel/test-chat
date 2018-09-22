<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Redis;

class MessagePosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $id;
    public $imageLink;

    /**
     * Create a new event instance.
     * @param  $message
     */
    public function __construct($message, $id, $imageLink)
    {
        $redis = Redis::connection();

        $redis->hmset("messages", ['message'.$id => json_encode($message)]);

        if (is_array($imageLink)) {
            $message['message'] = $imageLink;
        }

        $this->message = $message;
        $this->id = $id;
        $this->imageLink = $imageLink;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('chatroom');
    }
}
