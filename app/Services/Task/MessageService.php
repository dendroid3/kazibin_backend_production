<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Taskmessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageService{
    public function SendMessage(Request $request){
        if($request -> hasFile('documents')){
            $files = $request -> file('documents');
            $messages = array();
            $i = 0;
            foreach ($files as $file) {
                $uploadedFileUrl = cloudinary()->upload($request->file('documents')[$i]->getRealPath())->getSecurePath();
                $message = new Taskmessage();
                $message -> id = Str::orderedUuid() -> toString();
                $message -> user_id = Auth::user() -> id;
                $message -> type = $uploadedFileUrl;
                $message -> task_id = $request -> task_id;
                $message -> message = $request -> file('documents')[$i] -> getClientOriginalName();
                $message -> save();
        
                array_push($messages, $message);
                $i++;
            }
            return ['messages' => $messages, 'status' => 200, 'files' => true];
        } else {
            $message = new Taskmessage;
            $message -> id = Str::orderedUuid() -> toString();
            $message -> user_id = Auth::user() ->id;
            $message -> task_id = $request -> task_id;
            $message -> message = $request -> message;
            $message -> type = 'text';
            $message -> save();
            return ['message' => $message, 'status' => 200];
        }
    }

    public function GetMessages(Request $request){
        $messages = Taskmessage::query()
                    -> where('task_id', $request -> task_id)     
                    -> orderBy('created_at', 'ASC') 
                    -> get();

        foreach ($messages as $message) {
            
            if((!$message -> read_at) && ($message -> user_id != Auth::user() -> id)){
                $message -> read_at = Carbon::now();
                $message -> push();
            }
            
            $message -> mine = $message -> user_id === Auth::user() -> id;
        }
        return ['messages' => $messages, 'status' => 200];
    }
}