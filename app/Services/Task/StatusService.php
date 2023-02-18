<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Models\Taskmessage;
use Illuminate\Support\Facades\Auth;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Events\TaskMarkedComplete;

class StatusService{
    public function markComplete(Request $request, LogCreationService $log_service){
        $task = Task::find($request -> task_id);
        $task -> status = 3;
        $task -> updated_at = Carbon::now();
        $task -> push();

        $writer = $task -> writer -> user;
        $writer -> writer_score = $writer -> writer_score + 1;
        $writer -> push();
        

        $broker_message = 'Your task ' . $task-> code . ": " . $task -> topic . ' marked complete.';
        $log_service -> createSystemMessage(
            Auth::user() -> id, 
            $broker_message,
            $task -> writer -> user -> id,
            'Task Completed'
        ); 

        $writer_message = $task -> broker -> user -> username . "'s task " . $task -> code . ": " . $task -> topic . ' was marked complete.';
        $log_service -> createSystemMessage(
            $task -> broker -> user -> id,
            $writer_message,
            Auth::user() -> id, 
            'Task Completed'
        ); 

        $task_message = new Taskmessage;
        $task_message -> id = Str::orderedUuid() -> toString();
        $task_message -> user_id = 1;
        $task_message -> task_id = $request -> task_id;
        $task_message -> message = '--- Task marked as completed. ---';
        $task_message -> save();

        $reciever_id = Auth::user() -> writer -> id == $task -> writer_id ? $task -> broker -> user -> id : $task -> writer -> user -> id;

        $from_broker = Auth::user() -> writer -> id == $task -> writer_id ? false : true;

        $system_message = $from_broker ? $writer_message : $broker_message;

        event(new TaskMarkedComplete($task_message, $reciever_id, $from_broker, $system_message));
    
        return ['message' => $broker_message, 'status' => 200];

    }

    public function markCancel(Request $request, LogCreationService $log_service){

    }

    public function markInvoiced(Request $request, LogCreationService $log_service){

    }

    public function markPaid(Request $request, LogCreationService $log_service){
        
    }
}