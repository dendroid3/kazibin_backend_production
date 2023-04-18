<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $reciever_id;
    public $from_broker;
    public $system_message;
    public $task_id;

    public function __construct($message, $reciever_id, $from_broker, $system_message, $task_id)
    {
        $this -> message = $message;
        $this -> reciever_id = $reciever_id;
        $this -> from_broker = $from_broker;
        $this -> system_message = $system_message;
        $this -> task_id = $task_id;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this -> message,
            'from_broker' => $this -> from_broker,
            'system_message' => $this -> system_message,
            'title' => 'Task Message',
            'id' => $this -> task_id
        ];
    }
    
    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> reciever_id);
    }
}
