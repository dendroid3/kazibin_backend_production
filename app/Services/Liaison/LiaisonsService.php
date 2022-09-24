<?php

namespace App\Services\Liaison;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Facades\DB;

use App\Models\Liaisonrequest;
// use App\Models\Requestmessage;
// use App\Models\User;
use App\Models\Writer;
use App\Models\Broker;
use Illuminate\Support\Facades\Log;

class LiaisonsService {
  public function sendRequestToWriter(Request $request, LogCreationService $log_service){
  
    $liaison_request = new Liaisonrequest;
    $liaison_request -> id =  Str::orderedUuid() -> toString();
    $liaison_request -> initiator_id = Auth::user() -> id;
    $liaison_request -> broker_id = Auth::user() -> broker -> id;
    $liaison_request -> writer_id = $request -> writer_id;
    $liaison_request -> save();

    $log_service -> createLogOnRequestToWriter($liaison_request, Auth::user());

    return $liaison_request;
  }

  public function sendRequestToBroker(Request $request, LogCreationService $log_service){

    $liaison_request = new Liaisonrequest;
    $liaison_request -> id =  Str::orderedUuid() -> toString();
    $liaison_request -> initiator_id = Auth::user() -> id;
    $liaison_request -> writer_id = Auth::user() -> writer -> id;
    $liaison_request -> broker_id = $request -> broker_id;
    $liaison_request -> save();

    $log_service -> createLogOnRequestToBroker($liaison_request, Auth::user());

    return $liaison_request;
  }

  public function getLiaisonRequests(){

    $writers_requests = Liaisonrequest::query()
                        -> where('broker_id', Auth::user() -> broker -> id)
                        -> orderBy('created_at', 'DESC')
                        -> take(10)
                        -> get();
    if($writers_requests){

      foreach ($writers_requests as $writer_request) {
        $last_message = DB::table('requestmessages') -> where([
          ['broker_id', '=', $writer_request -> broker_id],
          ['writer_id', '=', $writer_request -> writer_id]
        ]) -> select('user_id', 'message', 'created_at') -> orderBy('created_at', 'DESC') -> take(1) -> get()[0]; 
        $writer_request -> last_message = $last_message;
        $writer_request -> writer = Writer::find($writer_request -> writer_id) -> user;
        $writer_request -> mine = Auth::user() -> id == $writer_request -> initiator_id;
        
        if($writer_request  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
          $writer_request -> unread_message = true;
        }

      }

    }

    $brokers_requests = Liaisonrequest::query()
                        -> where('writer_id', Auth::user() -> writer -> id)
                        -> orderBy('created_at', 'DESC')
                        -> take(10)
                        -> get();

    if($brokers_requests){
      foreach ($brokers_requests as $broker_request) {
        $last_message = DB::table('requestmessages') -> where([
          ['broker_id', '=', $broker_request -> broker_id],
          ['writer_id', '=', $broker_request -> writer_id]
        ]) -> select('user_id', 'message', 'created_at') -> orderBy('created_at', 'DESC') -> take(1) -> get()[0];
        $broker_request -> last_message = $last_message;
        $broker_request -> broker = Broker::find($broker_request -> broker_id) -> user;
        $broker_request -> mine = Auth::user() -> id == $broker_request -> initiator_id;
        
        if($broker_request  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
          $broker_request -> unread_message = true;
        }
      }
    
    }

    return [
      'writers_requests' => ($writers_requests) ? $writers_requests : null, 
      'brokers_requests' => ($brokers_requests) ? $brokers_requests : null
    ];
  }

  public function getLiaisonRequestsPaginated(Request $request){
    switch ($request -> filter_option) {
      case 'to brokers':
        $requests = Liaisonrequest::query()
          -> where('writer_id', Auth::user() -> writer -> id)
          -> where('initiator_id', Auth::user() -> id)
          -> orderBy('created_at', 'DESC')
          -> paginate(10);

        foreach ($requests as $broker_request) {
          $last_message = DB::table('requestmessages') -> where([
            ['broker_id', '=', $broker_request -> broker_id],
            ['writer_id', '=', $broker_request -> writer_id]
          ]) -> select('user_id', 'message', 'created_at') -> orderBy('created_at', 'DESC') -> take(1) -> get()[0];
          $broker_request -> last_message = $last_message;
          $broker_request -> broker = Broker::find($broker_request -> broker_id) -> user;
          $broker_request -> mine = Auth::user() -> id == $broker_request -> initiator_id;
          
          if($broker_request  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
            $broker_request -> unread_message = true;
          }
        }

        return $requests;

        break;

      case 'from brokers':
        $requests = Liaisonrequest::query()
          -> where('writer_id', Auth::user() -> writer -> id)
          -> where('initiator_id', '!=' , Auth::user() -> id)
          -> orderBy('created_at', 'DESC')
          -> paginate(10);

          foreach ($requests as $broker_request) {
            $last_message = DB::table('requestmessages') -> where([
              ['broker_id', '=', $broker_request -> broker_id],
              ['writer_id', '=', $broker_request -> writer_id]
            ]) -> select('user_id', 'message', 'created_at') -> orderBy('created_at', 'DESC') -> take(1) -> get()[0];
            $broker_request -> last_message = $last_message;
            $broker_request -> broker = Broker::find($broker_request -> broker_id) -> user;
            $broker_request -> mine = Auth::user() -> id == $broker_request -> initiator_id;
            
            if($broker_request  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $broker_request -> unread_message = true;
            }
          }

          return $requests;

        break;
      case 'to writers':
        $requests = Liaisonrequest::query()
          -> where('broker_id', Auth::user() -> broker -> id)
          -> where('initiator_id', Auth::user() -> id)
          -> orderBy('created_at', 'DESC')
          -> paginate(10);

          foreach ($requests as $writer_request) {
            $last_message = DB::table('requestmessages') -> where([
              ['broker_id', '=', $writer_request -> broker_id],
              ['writer_id', '=', $writer_request -> writer_id]
            ]) -> select('user_id', 'message', 'created_at') -> orderBy('created_at', 'DESC') -> take(1) -> get()[0]; 
            $writer_request -> last_message = $last_message;
            $writer_request -> writer = Writer::find($writer_request -> writer_id) -> user;
            $writer_request -> mine = Auth::user() -> id == $writer_request -> initiator_id;
            
            if($writer_request  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $writer_request -> unread_message = true;
            }
    
          }

          return $requests;
        break;
            
      default: #from writers
      Log::info('ins');
        $requests = Liaisonrequest::query()
          -> where('broker_id', Auth::user() -> broker -> id)
          -> where('initiator_id', '!=' ,Auth::user() -> id)
          -> orderBy('created_at', 'DESC')
          -> paginate(10);
          
        foreach ($requests as $writer_request) {
          $last_message = DB::table('requestmessages') -> where([
            ['broker_id', '=', $writer_request -> broker_id],
            ['writer_id', '=', $writer_request -> writer_id]
          ]) -> select('user_id', 'message', 'created_at') -> orderBy('created_at', 'DESC') -> take(1) -> get()[0]; 
          $writer_request -> last_message = $last_message;
          $writer_request -> writer = Writer::find($writer_request -> writer_id) -> user;
          $writer_request -> mine = Auth::user() -> id == $writer_request -> initiator_id;
          
          if($writer_request  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
            $writer_request -> unread_message = true;
          }
  
        }

        return $requests;
        break;
    }
  }

  public function rejectRequestFromBroker($request, $log_service){

    $this -> changeRequestStatus($request -> broker_id, Auth::user() -> writer -> id, 3);
    $broker = Broker::find($request -> broker_id) -> user;
    
    $my_message = $log_service -> createSystemMessage(Auth::user() -> id,
    'You rejected request from broker, '. $broker -> username . ' code: ' . $broker -> code,
     $request -> broker_id,
     'Request Rejected'
    );
    
    $writer = Auth::user();

    $message_to_broker = $log_service -> createSystemMessage($request -> broker_id, 
    'Your request to writer, ' . $writer -> username .' code: ' . Auth::user() -> code . ' has been rejected.', 
    Auth::user() -> id,
    'Request Rejected');

    return  $my_message -> message;

  }

  public function rejectRequestFromWriter($request, $log_service){
    $this -> changeRequestStatus(Auth::user() -> broker -> id, $request -> writer_id, 3);

    $writer = Writer::find($request -> writer_id) -> user;
    
    $my_message = $log_service -> createSystemMessage(
    Auth::user() -> id, 
    'You rejected request from writer, ' . $writer -> username . ' code ' . $writer -> code, 
    $request -> writer_id,
    'Request Rejected');

    $broker = Auth::user(); 

    $system_message = $log_service -> createSystemMessage(
    $request -> writer_id, 
    'Your request to broker, '. $broker -> username. ' code ' . Auth::user() -> code . ' has been rejected', 
    Auth::user() -> id,
    'Request Rejected');

    return  $my_message -> message;

  }

  public function attachBrokerToMe($request, $log_service){
    $user = Auth::user();
    // return $user;
    $check = DB::table('broker_writer') -> where([
      ['broker_id', '=', $request -> broker_id],
      ['writer_id', '=', $user -> writer -> id],
    ]) -> exists();
    if(!$check){
      
      DB::table('broker_writer')->insert([
        'broker_id' => $request -> broker_id, 
        'writer_id' => $user -> writer -> id,
        'cost_per_page' => 300
      ]);

      // Auth::user() -> writer -> brokers() -> attach($request -> broker_id);
      $this -> changeRequestStatus($request -> broker_id, $user -> writer -> id, 4); //broker_id , writer_id
      
      $broker = Broker::find($request -> broker_id) -> user;
      
      $log_service -> createSystemMessage(
        $user -> id, 
        'You accepted request from broker, code ' . $broker -> code . " " . $broker -> username . " has been added to your network", 
        $request -> broker_id,
        'Request Accepted'
      );
      
      $log_service -> createSystemMessage(
        $request -> broker_id, 
        'Your request to writer code' . $user -> code . ' has been accepted. ' . $user -> username . ' has been added to your network', 
        $user -> id,
        'Request Accepted'
      );

      return true;
    }
    
    return false;
  }

  public function attachWriterToMe($request, $log_service){
    $broker = Auth::User();
    $check = false;

    if(!$check){
      $writer = Writer::find($request -> writer_id) -> user;
      $broker -> broker -> writers() -> attach($request -> writer_id, ['cost_per_page' => 350]);
    $this -> changeRequestStatus($broker -> broker -> id, $request -> writer_id, 4);//broker_id , writer_id

    $broker_message =  'You accepted request from writer, code: ' . $writer -> code ." " . $writer -> username . " has been added to your network";
      
    $log_service -> createSystemMessage(
      $broker -> id, 
      $broker_message,
      $writer -> id,
      'Request Accepted'
    );
    $log_service -> createSystemMessage(
      $writer ->id, 
      'Your request to broker, code: ' . $broker -> code . ' has been accepted.' . $broker -> name . " has been added to your network",
      $broker ->id,
      'Request Accepted'
    );

    return $broker_message;  

    } 

    return false;

  }
  
  public function changeRequestStatus($broker_id, $writer_id, $status){
    DB::table('liaisonrequests') -> where([
      ['broker_id', '=', $broker_id],
      ['writer_id', '=', $writer_id]
    ]) -> update([
      'status' => $status
    ]);
  }

}