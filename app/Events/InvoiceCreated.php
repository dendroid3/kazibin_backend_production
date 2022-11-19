<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InvoiceCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $from_broker;
    public $system_message;
    public $tasks_signature;

    public function __construct($user_id, $from_broker, $system_message, $tasks_signature)
    {
        $this -> user_id = $user_id;
        $this -> from_broker = $from_broker;
        $this -> system_message = $system_message;
        $this -> tasks_signature = $tasks_signature;
    }

    public function broadcastWith(){
        return [
            'from_broker' => $this -> from_broker,
            'system_message' => $this -> system_message,
            'tasks_signature' => $this -> tasks_signature,
            'title' => 'Invoice Created'
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> user_id);
    }

}
