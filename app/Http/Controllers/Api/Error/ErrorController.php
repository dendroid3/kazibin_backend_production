<?php

namespace App\Http\Controllers\Api\Error;

use App\Models\Error;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\Telegram\BroadcastService;


class ErrorController extends Controller
{
    public function logError(Request $request, BroadcastService $broadcast_channel){

        $error = new Error;
        $error -> user_phone_number = $request -> user_phone_number;
        $error -> message = $request -> message;
        $error -> error_code = $request -> error_code;
        $error -> page = $request -> page;
        $error -> action = $request -> action;
        $error -> url = $request -> url;
        $error -> save();

        // $broadcast_channel -> braodcastToErrorChannel($request -> all());

    }
}
