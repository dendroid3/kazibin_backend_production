<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Services\Task\MessageService;

class MessageController extends Controller
{
    public function sendMessage(Request $request, MessageService $message_service){
        return response() -> json(
            $message_service -> SendMessage($request)
        );
    }

    public function getMessages(Request $request, MessageService $message_service){
        return response() -> json(
            $message_service -> GetMessages($request)
        );
    }
}
