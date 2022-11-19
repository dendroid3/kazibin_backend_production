<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reciever_id;
    public $system_message;
    public $offer_id;
   
    public function __construct($reciever_id, $system_message, $offer_id)
    {
        $this -> reciever_id = $reciever_id;

        $this -> system_message = $system_message;

        $this -> offer_id = $offer_id;
    }

    public function broadcastWith(){
        return [
            'offer_id' => $this -> offer_id,
            'system_message' => $this -> system_message,
            'title' => 'Offer Rejected'
        ];
    }
    
    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> reciever_id);
    }
}
