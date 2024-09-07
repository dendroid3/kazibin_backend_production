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

class MpesaTransactionComplete implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user_id;
    public $code;

    public function __construct($message, $user_id, $code)
    {
        Log::info("called in event");
        $this -> message = $message;
        $this -> user_id = $user_id;
        $this -> code = $code;
    }

    public function broadcastWith(){
        return [
            'message' => $this -> message,
            'title' => 'Transaction Complete',
            'code' => $this -> code
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('private_notification_' . $this -> user_id);
    }
}
