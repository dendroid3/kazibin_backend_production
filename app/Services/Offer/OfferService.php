<?php

namespace App\Services\Offer;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Taskoffer;
use App\Models\Task;
use App\Models\Broker;
use App\Models\Taskoffermessage;
use App\Models\User;
use App\Models\Taskmessage;
use App\Models\Tasktimestamp;

class OfferService
{
  public function create($task, LogCreationService $log_service, $taker)
  {
    $offer = new Taskoffer();
    $offer -> id = Str::orderedUuid() -> toString();
    $offer -> task_id = $task -> id;
    $offer -> writer_id = $taker;
    $offer -> save();

    $log_service -> createOfferLog($offer, $task);

    return ['validated' => true, 'created' => true];
  }

  public function rejectOffer(Request $request, LogCreationService $log_service)
  {
    $offer = Taskoffer::find($request -> offer_id);
    $offer -> status = 3;
    $offer -> updated_at = Carbon::now();
    $offer -> push();

    $task = Task::find($request -> task_id);
    $task -> updated_at = Carbon::now();
    $task -> push();

    $writer_message = "You rejected " . $task -> broker -> user -> username . "'s offer on task " .
                      $task -> code . ": " . $task ->topic;

    $log_service -> createSystemMessage(
      Auth::user() -> id, 
      $writer_message,
      $task -> broker -> user -> id,
      'Offer Rejected'
    ); 

    $broker_message = $offer -> writer -> user -> username . " rejected your offer on task " . $task -> code .  ": " . $task -> topic;

    $log_service -> createSystemMessage(
      $task -> broker -> user -> id,
      $broker_message,
      Auth::user() -> id, 
      'Offer Rejected'
    );

    return $writer_message;
  }

  public function acceptOffer(Request $request, LogCreationService $log_service){
    $offer = Taskoffer::find($request -> offer_id);

    $task = Task::find($request -> task_id);
    $task -> writer_id = Auth::user() -> Writer -> id;
    $task -> updated_at = Carbon::now();
    $task -> status = 2;
    $task -> push();

    $task_timestamp = new Tasktimestamp;
    $task_timestamp -> task_id = $task -> id;
    $task_timestamp -> assigned_at = Carbon::now();
    $task_timestamp -> save();

    $task -> broker -> User;
    $task -> files;

    $brokers_message ='Offer on job code ' . $task -> code . ' has been accepted by writer code: ' . Auth::user() -> code;

    $log_service -> createSystemMessage($task -> Broker -> User -> id, $brokers_message, $task -> id, 'Offer Accepted');

    $writers_message = 'You accepted offer on task code ' . $task -> code;

    $log_service -> createSystemMessage(Auth::user() -> id, $writers_message, $task -> id, 'Offer Accepted');

    $sorry_message = 'Offer on task code: ' . $task -> code . ' pulled after task was accepted by another writer';

    foreach ($task -> Offers as $offer) {
      if($offer -> writer_id == $task -> writer_id){
        $this -> migrateOfferMessagesToJobMessages($offer, $task -> id);
        $offer -> status = 4;
      } else {
        $offer -> status = 5;
        $log_service -> createSystemMessage($offer -> writer_id, $sorry_message, $offer -> id, 'Offer Pulled');

      }
      $offer -> push();
    }

    return $writers_message;
  }

  public function cancelOffer(Request $request, LogCreationService $log_service)
  {
    
    $offer = Taskoffer::find($request -> offer_id);
    $offer -> status = 2;
    $offer -> updated_at = Carbon::now();
    $offer -> push();

    $task = Task::find($request -> task_id);
    $task -> updated_at = Carbon::now();
    $task -> push();

    $writer_message = $task -> broker -> user -> username . " canceled your offer on task " . $task -> code .  ": " . $task -> topic;


    $log_service -> createSystemMessage(
      Auth::user() -> id, 
      $writer_message,
      $task -> broker -> user -> id,
      'Offer Canceled'
    ); 

    $broker_message = "You canceled " . $offer -> writer -> user -> username . "'s offer on task " .
                      $task -> code . ": " . $task ->topic;

    $log_service -> createSystemMessage(
      $task -> broker -> user -> id,
      $broker_message,
      Auth::user() -> id, 
      'Offer Canceled'
    );

    return $broker_message;
  }

  public function getMine(){
    $task_offers = Taskoffer::query() 
                -> where('writer_id', Auth::user() -> writer -> id) 
                -> orderBy('created_at', 'desc') 
                -> select('created_at', 'task_id', 'id', 'status')
                -> take(10)
                -> get();

    if($task_offers){
      foreach ($task_offers as $task_offer) {
            $task_offer -> task -> broker -> user;
            $task_offer -> messages -> first();
            
            if($task_offer  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $task_offer -> unread_message = true;
            }
        }
        //  orderBy('updated_at', 'desc')
        return $task_offers;
            // 'job_id' => $task_offer -> job_id,

    } else {
        return[
            'offers' => null
        ];
    }
  }

  public function getMinePaginated(Request $request){
    
    $query = Taskoffer::where('writer_id', Auth::user() -> writer -> id);
    if($request['status']){
      $query -> where('status', $request['status']);
    } 
    $query -> orderBy('updated_at', 'DESC');
    $query -> select('created_at', 'task_id', 'id', 'status');
    $task_offers = $query-> paginate(10);
    
    if($task_offers){
      foreach ($task_offers as $task_offer) {
            $task_offer -> task -> broker -> user;
            $task_offer -> messages -> first();
            
            if($task_offer  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $task_offer -> unread_message = true;
            }
        }
        //  orderBy('updated_at', 'desc')
        return $task_offers;
            // 'job_id' => $task_offer -> job_id,

    } else {
        return[
            'offers' => null
        ];
    }
  }

  public function getOfferMessages(Request $request){
    // $messages = Taskoffer::
    
    $messages = Taskoffermessage::query()
    // DB::table('taskoffermessages')
                  -> where('taskoffer_id', $request -> task_offer_id) 
                  -> orderBy('created_at', 'ASC')
                  -> get();
    // find($request -> task_offer_id) -> messages;
    foreach ($messages as $message) {
      
        if((!$message -> read_at) && ($message -> user_id != Auth::user() -> id)){
          $message -> read_at = Carbon::now();
          $message -> push();
        }
        $message -> mine = $message -> user_id === Auth::user() -> id;
    }

    return $messages;
  }

  public function sendOfferMessages(Request $request){
    if($request -> hasFile('documents')){
      $files = $request -> file('documents');
      $messages = array();
      $i = 0;
      foreach ($files as $file) {
          $uploadedFileUrl = cloudinary()->upload($request->file('documents')[$i]->getRealPath())->getSecurePath();
          $task_offer_message = new Taskoffermessage();
          $task_offer_message -> id = Str::orderedUuid() -> toString();
          $task_offer_message -> user_id = Auth::user() -> id;
          $task_offer_message -> type = $uploadedFileUrl;
          $task_offer_message -> taskoffer_id = $request -> task_offer_id;
          $task_offer_message -> message = $request -> file('documents')[$i] -> getClientOriginalName();
          $task_offer_message -> save();
  
          array_push($messages, $task_offer_message);
          $i++;
      }
      return ['messages' => $messages, 'status' => 200, 'files' => true];

    } else {
      $task_offer_message = new Taskoffermessage();
      $task_offer_message -> id = Str::orderedUuid() -> toString();
      $task_offer_message -> user_id = Auth::user() -> id;
      $task_offer_message -> taskoffer_id = $request -> task_offer_id;
      $task_offer_message -> message = $request -> message;
      $task_offer_message -> type = 'text';
      $task_offer_message -> save();
      return ['message' => $task_offer_message, 'status' => 200];
    }

  }
  
  public function migrateOfferMessagesToJobMessages($offer, $task_id){

    $text_messages = DB::table('taskoffermessages')->where('taskoffer_id', $offer -> id)-> select(
      'message', 'user_id', 'created_at', 'type', 'delivered_at', 'read_at'
    ) ->get();

    foreach ($text_messages as $message) {
        $task_message = new Taskmessage;
        $task_message -> id = Str::orderedUuid() -> toString();
        $task_message -> user_id = $message -> user_id;
        $task_message -> task_id = $task_id;
        $task_message -> message = $message -> message;
        $task_message -> type = $message -> type;
        $task_message -> created_at = $message -> created_at;
        $task_message -> delivered_at = $message -> delivered_at;
        $task_message -> read_at = $message -> read_at;
        $task_message -> save();
    }

    $task_message = new Taskmessage;
    $task_message -> id = Str::orderedUuid() -> toString();
    $task_message -> user_id = 1;
    $task_message -> task_id = $task_id;
    $task_message -> message = '--- Job has been assigned from offer ---';
    $task_message -> save();
  }
}
