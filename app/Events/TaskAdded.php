<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    
    public function __construct($task)
    {
        $this-> task = $task;
    }

    public function broadcastWith()
    {
        return [
            'task' => $this -> task
        ];
    }
    
    public function broadcastOn()
    {
        return new Channel('public_notifications');
    }
}
