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


class BidMade implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;

    public $message;

    public $user_id;

  
    public function __construct($bid, $message, $user_id)
    {
        $this -> bid = $bid;
        $this -> message = $message;
        $this -> user_id = $user_id;
    }

    public function broadcastWith(){
        return [
            'bid' => $this -> bid,
            'message' => $this -> message,
            'title' => 'Bid Made'
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> user_id);
    }
}
