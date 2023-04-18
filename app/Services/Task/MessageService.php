<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Taskmessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Events\TaskMessageSent;

class MessageService{
    public function SendMessage(Request $request){
        $task = Task::find($request -> task_id);
        $reciever_id = Auth::user() -> writer -> id == $task -> writer_id ? $task -> broker -> user -> id : $task -> writer -> user -> id;
        $from_broker = Auth::user() -> writer -> id == $task -> writer_id ? false : true;
        $system_message = 'New message from ' . Auth::user()-> code . " : " . Auth::user() -> username . ", on task, " . $task -> code . " : " .$task -> topic . ":";

        if($request -> hasFile('documents')){
            $files = $request -> file('documents');
            $messages = array();
            $i = 0;
            foreach ($files as $file) {
                $uploadedFileUrl = Storage::disk('digitalocean')->putFile(Auth::user() -> code, $request->file('documents')[$i], 'public');
                $message = new Taskmessage();
                $message -> id = Str::orderedUuid() -> toString();
                $message -> user_id = Auth::user() -> id;
                $message -> type = 'https://kazibin.sfo3.digitaloceanspaces.com/' . $uploadedFileUrl;
                $message -> task_id = $request -> task_id;
                $message -> message = $request -> file('documents')[$i] -> getClientOriginalName();
                $message -> save();
        
                array_push($messages, $message);
                $i++;

                event(new TaskMessageSent($message, $reciever_id, $from_broker, $system_message, 565));

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

            event(new TaskMessageSent($message, $reciever_id, $from_broker, $system_message, 565));

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