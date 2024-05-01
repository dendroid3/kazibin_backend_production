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

class OfferMade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reciever_id;
    public $system_message;

    public function __construct($reciever_id, $system_message)
    {

        $this -> reciever_id = $reciever_id;
        $this -> system_message = $system_message;

        Log::info("In");
        Log::info($this -> reciever_id);
    }

    public function broadcastWith()
    {
        return [
            'system_message' => $this -> system_message,
            'title' => 'Task Offered'
        ];
    }
    
    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> reciever_id);
    }
}
