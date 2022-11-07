<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid_id;
    public $message;
    public $user_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bid_id, $broker_message, $user_id)
    {
        $this -> bid_id = $bid_id;

        $this -> message = $broker_message;

        $this -> user_id = $user_id;
    }

    public function broadcastWith(){
        return [
            'bid_id' => $this -> bid_id,
            'message' => $this -> message,
            'title' => 'Bid Rejected'
        ];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> user_id);
    }
}
