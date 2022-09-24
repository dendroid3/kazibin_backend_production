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

    public $system_message;

    public $user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bid, $system_message, $user_id)
    {
        Log::info('constructed');
        $this -> bid = $bid;
        $this -> system_message = $system_message;
        $this -> user_id = $user_id;
    }

    public function broadcastWith(){
        return [
            'bid' => $this -> bid,
            'system_message' => $this -> system_message
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
        // return new PrivateChannel('private_notification_974d522f-6246-4130-a43d-6a3939f8c372');
    }
}
