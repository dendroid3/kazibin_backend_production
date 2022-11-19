<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user_id;
    public $from_broker;
    public $system_message;

    public function __construct($message, $user_id, $from_broker, $system_message)
    {
        $this -> message = $message;
        $this -> user_id = $user_id;
        $this -> from_broker = $from_broker;
        $this -> system_message = $system_message;
    }

    public function broadcastWith(){
        return [
            'message' => $this -> message,
            'from_broker' => $this -> from_broker,
            'system_message' => $this -> system_message,
            'title' => 'Offer Message'
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> user_id);
    }

}