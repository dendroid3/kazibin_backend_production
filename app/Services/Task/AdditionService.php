<?php

namespace App\Services\Task;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\Task;
use App\Models\Taskmessage;
use App\Models\Taskfile;

use App\Events\TaskMessageSent;

use App\Services\Offer\OfferService;

class AdditionService 
{
  public function addInitialTaskDetails(Request $request){
    
    $validator = Validator::make($request->all(), [
      'topic' => ['required', 'min:5', 'bail'],
      'unit' => ['required', 'bail'],
      'type' => ['required', 'bail'],
      'instructions' => ['required', 'min:10', 'bail'],
    ]);

    if ($validator->fails()) {
      return ['validated' => false, 'errors' => $validator -> errors()];
    }

            
    $task = new Task;
    $task -> broker_id = Auth::user() -> broker -> id;
    $task -> status = 1;
    $task -> code = $request -> code ? strtoupper($request -> code) : strtoupper(Str::random(2)) . '-' . strtoupper(Str::random(3));
    $task -> topic = strtoupper($request -> topic);
    $task -> unit = strtoupper($request -> unit);
    $task -> type = $request -> type;
    $task -> instructions = $request -> instructions;
    $task -> save();

    //$task -> code = strtoupper(Str::random(2)) . '-' . strtoupper(Str::random(3)); 

    //$task -> push();

    return ['validated' => true, 'task' => $task];

  }

  public function addTaskFiles(Request $request){

    $files = $request -> file('documents');
    $file_urls = array();
    $i = 0;
    foreach ($files as $file) {
        $uploadedFileUrl = Storage::disk('digitalocean')->putFile(Auth::user() -> code, $request->file('documents')[$i], 'public');
        $task_file = new Taskfile;
        $task_file -> task_id = $request -> task_id;
        $task_file -> url = 'https://kazibin.sfo3.digitaloceanspaces.com/' .  $uploadedFileUrl;
        $task_file -> name =  $request -> file('documents')[$i] -> getClientOriginalName();
        $task_file -> save();

        array_push($file_urls, $task_file);
        $i++;
    }

    return $file_urls;
  }

  public function addPageCount(Request $request){

    $task = Task::find($request -> task_id);
    if($request -> full_pay){
        $task -> full_pay = $request -> full_pay;
        $task -> pages = null;
        $task -> page_cost = null;
    } else {
        $task -> pages = $request -> pages;
        $task -> page_cost = $request -> page_cost;
        $task -> full_pay = ($request -> pages) * ($request -> page_cost);
    }

    $task -> push();

    return $task;
  }

  public function addDeadline(Request $request){
    
    $task = Task::find($request -> task_id);
    $task -> expiry_time = $request -> expiry_time;
    $task -> push();

    return $task;
  }

  public function addPayInformation(Request $request){
    /*
      this step adds the payment mode for the task. This record will then be used to give the writer and broker reminders as well. 
        Date 28/05/1965 means payment on delivery
        Date 17/09/1997 means payment on approval
    */

    $task = Task::find($request -> task_id);
    $task -> pay_day = $request -> pay_day;
    $task -> push();

    return $task;
  }

  public function addDifficultyAndTakers(Request $request, OfferService $offer_service, LogCreationService $log_service){
    $task = Task::find($request -> task_id);
    $task -> takers = $request -> takers;
    if($request -> difficulty){
      $task -> difficulty = $request -> difficulty;
    }
    $task -> updated_at = Carbon::now();
    $task -> push();

    //create task log
    if(!$request -> reassigning){
      if($request -> takers){

        $takers = explode('_', $request -> takers);
        foreach ($takers as $taker) {
          if($taker){
            $offer_service -> create($task, $log_service, $taker);
          }
        }
        
      }

      $broker_message = $log_service -> createTaskLog($task);
      return ['validated' => true, 'message' => $broker_message, 'task' => $task];

    } else {
      $broker_message = "Task " . $task -> code . ": " . $task -> topic . " was offered to " . ( count(explode('_', $request -> takers)) - 1) ." writers from your writers network";
      $log_service -> createSystemMessage(
        Auth::user() -> id,
        $broker_message,
        $task -> id,
        'Task Offered'
      );
    }

    if($request -> reassigning){
      return ['validated' => true,'message' => $broker_message];
    }
  }

  public function deleteTask(Request $request, LogCreationService $log_service){
    $task = Task::find($request -> task_id);
    
    if(($task -> bids -> count() === 0) && ($task -> offers -> count() === 0) && ($task -> status === 1)){
      $broker_message = "You deleted task code " . $task -> code . ".";
      $log_service -> createSystemMessage(
        Auth::user() -> id,
        $broker_message,
        $task -> id,
        'Task Deleted'
      );

    } elseif($task -> bids && $task -> status === 1) {
      $broker_message = "You deleted task code " . $task -> code . ". All the " . $task -> bids -> count() . " bids were pulled as well.";

      $log_service -> createSystemMessage(
        Auth::user() -> id,
        $broker_message,
        $task -> id,
        'Task Deleted'
      );

      foreach ($task -> bids as $bids) {
        $writer_message = "Bid on " . $task -> code . ": " . $task -> topic . " pulled because " . Auth::user() -> username . " deleted the task.";

        $bid -> status = 2;
        $bid-> push();
        
        $log_service -> createSystemMessage(
          $bid -> writer -> user -> id,
          $writer_message,
          Auth::user() -> id,
          'Task Deleted'
        );
      }

    } elseif($task -> offers && $task -> status === 1){
      $broker_message = "You deleted task code " . $task -> code . ". All the " . $task -> offers -> count() . " offers were canceled as well.";

      $log_service -> createSystemMessage(
        Auth::user() -> id,
        $broker_message,
        $task -> id,
        'Bid Pulled'
      );

      foreach ($task -> offers as $offers) {
        $writer_message = "Offer on " . $task -> code . ": " . $task -> topic . " pulled because " . Auth::user() -> username . " deleted the task.";

        $offer -> status = 2;
        $offer -> push();
        
        $log_service -> createSystemMessage(
          $offer -> writer -> user -> id,
          $writer_message,
          Auth::user() -> id,
          'Offer Canceled'
        );
      }

    } else {
      return "Task cannot be deleted.";
    }
    $task -> delete();

    return $broker_message;

  }

  public function changeDeadline(Request $request){
    
    $task = Task::find($request -> task_id);
    $task -> expiry_time = $request -> expiry_time;
    $task -> push();

    $task_message = new Taskmessage;
    $task_message -> id = Str::orderedUuid() -> toString();
    $task_message -> user_id = 1;
    $task_message -> type = 'text';
    $task_message -> task_id = $request -> task_id;
    $task_message -> message = '--- Deadline changed to ' . $request -> expiry_time . ' ---' ;
    
    if($task -> status > 1){
      $task_message -> save();
      event(new TaskMessageSent($task_message, $task -> writer -> user -> id, false, 'Change in deadline on task '. $task -> code . " : " . $task -> topic, 565));
    
    }
    ($task -> status > 1) ? $message_to_send = $task_message : $message_to_send = "Deadline Changed";

    return [
      'message' => $message_to_send,
      'task' => $task
    ];

  }

  
  public function changePayment(Request $request){

    $task = Task::find($request -> task_id);
    
    $task_message = new Taskmessage;
    $task_message -> id = Str::orderedUuid() -> toString();
    $task_message -> user_id = 1;
    $task_message -> type = 'text';
    $task_message -> task_id = $request -> task_id;

    if($request -> full_pay){
        $task -> full_pay = $request -> full_pay;
        $task -> pages = null;
        $task -> page_cost = null;
        
        $task_message -> message = '--- Payment terms changed to ' . $request -> full_pay . 'KES for the whole task---' ;
    } else {
        $task -> pages = $request -> pages;
        $task -> page_cost = $request -> page_cost;
        $task -> full_pay = ($request -> pages) * ($request -> page_cost);

        $task_message -> message = '--- Payment terms changed to ' . $request -> pages . ' pages at ' .
        $request -> page_cost . 'KES, totaling ' .  ($request -> pages) * ($request -> page_cost) . ' KES ---' ;
    }

    if($task -> status > 1){
      $task_message -> save();
      event(new TaskMessageSent($task_message, $task -> writer -> user -> id, false, 'Change in payment details on task ' . $task -> code . " : " . $task -> topic, 565));
    }

    $task -> push();

    ($task -> status > 1) ? $message_to_send = $task_message : $message_to_send = "Payment Details Changed";
    return [
      'message' => $message_to_send,
      'task' => $task
    ];
  }
}
