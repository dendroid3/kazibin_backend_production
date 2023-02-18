<?php

namespace App\Services\Bid;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Events\BidMade;
use App\Events\BidPulled;
use App\Events\BidRejected;
use App\Events\MyBidAccepted;
use App\Events\OtherBidAccepted;
use App\Events\BidMessageSent;

use App\Models\Bid;
use App\Models\Bidmessage;
use App\Models\Writer;
use App\Models\Task;
use App\Models\Taskmessage;
use App\Models\Revenue;
use App\Models\Transaction;
use App\Models\Tasktimestamp;

use App\Services\SystemLog\LogCreationService;

class BidsService {

  public function registerBid(Request $request, LogCreationService $log_service){
    $bidded = Auth::user() -> writer -> bids -> pluck('task_id');
    foreach ($bidded as $old_id) {
        if($old_id == $request -> task_id){
            return 'false';
        }
    }
    $bid = new Bid;
    $bid -> id = Str::orderedUuid() -> toString();
    $bid -> writer_id = Auth::user() -> writer -> id;
    $bid -> task_id = $request -> task_id;
    $bid -> save();

    Log::info($request -> all());
    $task = Task::find($request -> task_id);

    $broker = $task -> broker -> user;
    $writer_message = 'Bid made to broker username: ' . $broker -> username . ". Code: " .
                        $broker -> code . '. On task topic: ' . $task -> topic . ". Code: " . $task -> code . 
                        ". Worth: " . $task -> full_pay;

    $broker_message = "Bid made on your task, " . $task -> code . ": " . $task -> topic . ". By " .
                        Auth::user() -> code . ": " . Auth::user() -> username . ".";
    $log_service -> createSystemMessage(
                                          Auth::user() -> id, 
                                          $writer_message,
                                          $broker -> id,
                                          'Bid Made'
                                        );

    $log_service -> createSystemMessage(
                                          $broker -> id, 
                                          $broker_message,
                                          Auth::user() -> id,
                                          'Bid Made'
                                        );

    $message = new Bidmessage();
    $message -> id = Str::orderedUuid() -> toString();
    $message -> user_id = 1;
    $message -> bid_id = $bid -> id;
    $message -> message = "Proposal: " . $request -> proposal;
    $message -> save();

    $transaction = new Transaction;
    $transaction -> user_id = Auth::user() -> id;
    $transaction -> type = "Credit";
    $transaction -> bid_id = $bid -> id;
    $transaction -> description = 'Amount charged to bid on task topic '. $task -> topic . ". Code: " . $task -> code . ". Worth: " . $task -> full_pay;
    $transaction -> amount = $request -> bid_cost;
    $transaction -> save();

    $revenue = new Revenue;
    $revenue -> transaction_id = $transaction -> id;
    $revenue -> type = "Bid";
    $revenue -> amount = $request -> bid_cost;
    $revenue -> save();

    event(new BidMade($bid, $broker_message, $bid -> task -> broker -> user -> id));

    return [
      'success' => true,
      'bid' => $bid,
      'message' => $writer_message,
      'status' => 200
    ];
  }

  public function sendBidMessage(Request $request){
    $bid = Bid::find($request -> bid_id);

    $reciever_id = Auth::user() -> writer -> id == $bid -> writer_id ? $bid -> task -> broker -> user -> id : $bid -> writer -> user -> id;
    $from_broker = Auth::user() -> writer -> id == $bid -> writer_id ? false : true;
    $system_message = 'New message from ' . Auth::user()-> code . " : ". Auth::user() -> username . ", on bid on task, " . $bid -> task -> code . " : " .$bid -> task -> topic . ".";

    if($request -> hasFile('documents')){
      $files = $request -> file('documents');
      $messages = array();
      $i = 0;
      foreach ($files as $file) {
          $uploadedFileUrl = Storage::disk('digitalocean')->putFile(Auth::user() -> code, $request->file('documents')[$i], 'public');
          $message = new Bidmessage();
          $message -> id = Str::orderedUuid() -> toString();
          $message -> user_id = Auth::user() -> id;
          $message -> type = 'https://kazibin.sfo3.digitaloceanspaces.com/' . $uploadedFileUrl;
          $message -> bid_id = $request -> bid_id;
          $message -> message = $request -> file('documents')[$i] -> getClientOriginalName();
          $message -> save();
  
          array_push($messages, $message);
          $i++;

          event(new BidMessageSent($message, $reciever_id, $from_broker, $system_message));

      }
      return ['messages' => $messages, 'status' => 200, 'files' => true];
    } else {
      $message = new Bidmessage();
      $message -> id = Str::orderedUuid() -> toString();
      $message -> user_id = Auth::user() -> id;
      $message -> bid_id = $request -> bid_id;
      $message -> type = 'text';
      $message -> message = $request -> message;
      $message -> save();

      event(new BidMessageSent($message, $reciever_id, $from_broker, $system_message));

      return ['message' => $message, 'status' => 200];
  }
  }

  public function getBidMessages(Request $request){

    $messages =   Bidmessage::query()
                  -> where('bid_id', $request -> bid_id) 
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

  public function getMyBids(){
    //order by updated
    $bids = Auth::user() -> writer -> bids -> take(10); # -> sortDesc('updated_at') ;
    
    foreach ($bids as $bid) {
      $bid -> task -> broker -> user;

      if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
        $bid -> unread_message = true;
      }
    };

    return $bids;
  }

  public function getMyBidsPaginated(Request $request){
    
    $query = Bid::where('writer_id', Auth::user() -> writer -> id);
    if($request['status']){
      $query -> where('status', $request['status']);
    } 
    $query  -> orderBy('updated_at', 'DESC');
    $bids = $query-> paginate(10);
    // Log::info($bids);
    
    foreach ($bids as $bid) {
      $bid -> task -> broker -> user;

      if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
        $bid -> unread_message = true;
      }
    };
    return $bids;
  }

  public function acceptBid(Request $request,  LogCreationService $log_service){
    $bid = Bid::find($request -> bid_id);

    $writer =  $bid -> writer -> user; 
    
    $task = $bid -> task; 
    $task -> writer_id = $bid -> writer_id;
    $task -> status = 2;
    $task -> push();

    $task_timestamp = new Tasktimestamp;
    $task_timestamp -> task_id = $task -> id;
    $task_timestamp -> assigned_at = Carbon::now();
    $task_timestamp -> save();

    $writer_message = 'Bid on ' . Auth::user() -> name . "'s task code: " . $task -> code . " has been accepted";
    $log_service -> createSystemMessage(
      $writer -> id, 
      $writer_message,
      Auth::user() -> id,
      'Bid Accepted'
    );

    event(new MyBidAccepted($bid -> id, $writer_message, $bid -> writer -> user -> id));

    $broker_message = 'You accepted ' . $writer -> username . "'s bid on task, " . $task -> code . ": " . $task -> topic;
    $log_service -> createSystemMessage(
      Auth::user() -> id, 
      $broker_message,
      $writer -> id,
      'Bid Accepted'
    );

    $bids = $task -> bids;
    $dropped_bid_message = 'Bid on ' . Auth::user() -> username . "'s task code: " . $task -> code . " has been drop after task was assigned to another writer. Sorry ;(";

    foreach ($bids as $bid) {
        if(($bid -> writer_id) === ($task -> writer_id)){
          $bid -> updated_at = Carbon::now();
          $bid -> status = 4;
          $bid -> push();
          $this -> migrateBidMessagesToTaskMessages($bid);
        } else {
          $bid -> updated_at = Carbon::now();
          $bid -> status = 5;
          $bid -> push();

          $log_service -> createSystemMessage(
            Writer::find($bid -> writer_id) -> user -> id, 
            $dropped_bid_message,
            Auth::user() -> id,
            'Bid Dropped'
          );
      
          event(new OtherBidAccepted($bid -> id, $dropped_bid_message, $bid -> writer -> user -> id));
        }
    }

    return [
        'status' => 200,
        'success' => true,
        'message' => $broker_message
    ];

  }

  public function pullBid(Request $request, LogCreationService $log_service){
    $bid = Bid::find($request -> bid_id);
    $bid -> status = 2;
    $bid -> updated_at = Carbon::now();
    $bid -> push();

    $task = $bid -> task;

    $writer_message = 'You pulled bid on a ' . $task -> broker -> user -> username ."'s " . $task -> unit . $task -> type . 
    ' task, ' . $task -> code .": ". $task -> topic . ".";

    $log_service -> createSystemMessage(
      Auth::user() -> id, 
      $writer_message,
      $task -> broker -> user -> id,
      'Bid Pulled'
    );

    $broker_message = $bid -> writer -> user -> username . ' pulled bid on task ' . $task -> code . ": " . $task -> topic;

    $log_service -> createSystemMessage(
      $task -> broker -> user -> id, 
      $broker_message,
      Auth::user() -> id,
      'Bid Pulled'
    );

    event(new BidPulled($bid -> id, $broker_message, $task -> broker -> user -> id));

    return [
      "status" => 200,
      "success" => true,
      "message" => $writer_message
    ];

  }

  public function rejectBid(Request $request, LogCreationService $log_service){
    $bid = Bid::find($request -> bid_id);
    $bid -> status = 3;
    $bid -> updated_at = Carbon::now();
    $bid -> push();

    $task = $bid -> task;

    $broker_message = 'You rejected ' . $bid -> writer -> user -> username . "'s bid on task " . 
                      $task -> code .": ". $task -> topic . ".";

    $log_service -> createSystemMessage(
      Auth::user() -> id, 
      $broker_message,
      $task -> broker -> user -> id,
      'Bid Rejected'
    );

    $writer_message = $bid -> Task -> broker -> user -> username . ' rejected your bid on task ' . $task -> code . ": " . $task -> topic;

    $log_service -> createSystemMessage(
      $bid -> writer -> user -> id, 
      $writer_message,
      Auth::user() -> id,
      'Bid Rejected'
    );

    event(new BidRejected($bid -> id, $writer_message, $bid -> writer -> user -> id));

    return [
      "status" => 200,
      "success" => true,
      "message" => $broker_message
    ];
  }

  public function migrateBidMessagesToTaskMessages($bid){
    // $messages = $this -> fetchBidMessages($bid -> id, $bid -> job -> broker_id) ; #bidd id user id
    
    $text_messages = DB::table('bidmessages') -> where('bid_id', $bid -> id) -> get();
    foreach ($text_messages as $text_message) {

        $task_message = new Taskmessage();
        $task_message -> id = Str::orderedUuid() -> toString();
        $task_message -> user_id = $text_message -> user_id;
        $task_message -> task_id = $bid -> task_id;
        $task_message -> type = $text_message -> type;
        $task_message -> message = $text_message -> message;
        $task_message -> delivered_at = $text_message -> delivered_at;
        $task_message -> read_at = $text_message -> read_at;
        $task_message -> created_at = $text_message -> created_at;
        $task_message -> save();
    }


    $task_message = new Taskmessage;
    $task_message -> id = Str::orderedUuid() -> toString();
    $task_message -> user_id = 1;
    $task_message -> task_id = $bid -> task_id;
    $task_message -> message = 'Task has been assigned from bid';
    $task_message -> save();

    return true;

  }


}
