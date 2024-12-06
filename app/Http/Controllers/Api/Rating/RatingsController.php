<?php

namespace App\Http\Controllers\Api\Rating;

use App\Http\Controllers\Controller;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Rating;
use App\Models\Writer;
use App\Models\Broker;
use App\Models\Task;
use App\Models\Taskmessage;

class RatingsController extends Controller
{
    public function Rate(Request $request, LogCreationService $log_service){
        $rating = new Rating;
        $rating -> task_id = $request -> task_id;
        $rating -> broker_id = $request -> broker_id;
        $rating -> writer_id = $request -> writer_id;
        $rating -> initiator_id = Auth::user() -> id;
        $rating -> rating = $request -> rating;
        $rating -> review = $request -> review;
        $rating -> save();

        $task = Task::find($request -> task_id);
        $task -> updated_at = Carbon::now();
        $task -> push();

        if(Auth::user() -> writer -> id == $request -> writer_id){
            $broker = Broker::find($request -> broker_id) -> user;
            $broker_message = Auth::user() ->username . ' rated you ' . $request -> rating . '/10 stars on task code: ' . $task -> code;
            $log_service -> createSystemMessage(
                $broker -> id, 
                $broker_message,
                Auth::user() -> id,
                'Rating Made'
            ); 
    
            $writer_message = 'You rated ' . $broker -> username . " " . $request -> rating . '/10 stars on task code: ' . $task ->code;
            $log_service -> createSystemMessage(
                Auth::user() -> id,
                $writer_message,
                $broker -> id, 
                'Rating Made'
            ); 
            
            $task_message = new Taskmessage;
            $task_message -> id = Str::orderedUuid() -> toString();
            $task_message -> user_id = 1;
            $task_message -> task_id = $request -> task_id;
            $task_message -> message = '---' . Auth::user() -> username . ' rated ' . $broker -> username . "'s perfomance on this task as " . $request -> rating . "/10 ---";
            $task_message -> save();

            return response() -> json(
                $writer_message
            );
            
        } else {
            $writer = Writer::find($request -> writer_id) -> user;
            $writer_message = Auth::user() ->username . ' rated you ' . $request -> rating . '/10 stars on task code: ' . $task -> code;
            $log_service -> createSystemMessage(
                $writer -> id, 
                $writer_message,
                Auth::user() -> id,
                'Rating Made'
            ); 
    
            $broker_message = 'You rated ' . $writer -> username . " " . $request -> rating . '/10 stars on task code: ' . $task ->code;
            $log_service -> createSystemMessage(
                Auth::user() -> id,
                $broker_message,
                $writer -> id, 
                'Rating Made'
            );
            $task_message = new Taskmessage;
            $task_message -> id = Str::orderedUuid() -> toString();
            $task_message -> user_id = 1;
            $task_message -> task_id = $request -> task_id;
            $task_message -> message = '---' . Auth::user() -> username . ' rated ' . $writer -> username . "'s perfomance on this task as " . $request -> rating . "/10 ---";
            $task_message -> save();

            return response() -> json(
                $broker_message
            );
        }
    }
}
