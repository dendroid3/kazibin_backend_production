<?php

namespace App\Http\Controllers\Api\Liaison;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Liaison\LiaisonMessagesService;

class LiaisonMessagesController extends Controller
{
    public function getRequestMessages(Request $request,LiaisonMessagesService $request_messages_service){
        
        return response() -> json([
            'messages' => $request_messages_service -> getRequestMessages($request),
        ]);

    }

    public function sendRequestMessage(Request $request,LiaisonMessagesService $request_messages_service){
        if($request->hasFile('documents')){
            
            return response() -> json([
                'files' => $request_messages_service -> sendRequestMessage($request),
                'status' => 200
            ]);

        } else {
            
            return response() -> json([
                'message' => $request_messages_service -> sendRequestMessage($request),
                'status' => 200
            ]);

        }
    }

    public function setCostPerPage(Request $request,LiaisonMessagesService $request_messages_service){
        
        return response() -> json([
            'message' => $request_messages_service -> setCPP($request),
            'status' => 200
        ]);
    }
}
