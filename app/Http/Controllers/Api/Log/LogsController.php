<?php

namespace App\Http\Controllers\Api\Log;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function getLogMessages(Request $request)
    {
        $messages = DB::table('logs') 
            -> where('user_id', Auth::user() -> id)
            -> orderBy('created_at', 'desc') 
            -> get();
        return response() -> json([
        'messages' => $messages,
        'status' => 200
        ]);
    }
}
