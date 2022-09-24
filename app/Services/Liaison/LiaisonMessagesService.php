<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Liaisonrequest;
use App\Models\Requestmessage;

class LiaisonMessagesService {
  public function getRequestMessages(Request $request){
    $text_messages = Requestmessage::query()
    // DB::table('requestmessages')
    -> where('liaisonrequest_id', $request -> request_id) 
    -> orderBy('created_at', 'ASC')
    -> get();
    foreach ($text_messages as $message) {
      
      if((!$message -> read_at) && ($message -> user_id != Auth::user() -> id)){
        $message -> read_at = Carbon::now();
        $message -> push();
      }
      $message -> mine = $message -> user_id === Auth::user() -> id;
    }

    $files = null;
    $messages_draft = collect($text_messages);
    $messages = $messages_draft -> merge($files) -> sortBy('created_at');
    $messages_array = array();
    foreach ($messages as $message) {
      array_push($messages_array, $message);
    }
    return $messages_array;
  }

  public function sendRequestMessage(Request $request){
    $liaison_request = Liaisonrequest::find($request -> request_id);
    $liaison_request_message = new Requestmessage;
    $liaison_request_message -> user_id = Auth::user() -> id;
    $liaison_request_message -> liaisonrequest_id = $liaison_request -> id;
    $liaison_request_message -> broker_id = $liaison_request -> broker_id;
    $liaison_request_message -> writer_id = $liaison_request -> writer_id;
    $liaison_request_message -> message = $request -> message;
    $liaison_request_message -> save();

    return $liaison_request_message;
  }

  public function setCPP(Request $request){
    $liaison_request = Liaisonrequest::find($request -> request_id);
    $liaison_request_message = new Requestmessage;
    $liaison_request_message -> user_id = 1;
    $liaison_request_message -> liaisonrequest_id = $liaison_request -> id;
    $liaison_request_message -> broker_id = $liaison_request -> broker_id;
    $liaison_request_message -> writer_id = $liaison_request -> writer_id;
    $liaison_request_message -> message = '--- New CPP of '. $request -> cpp . ' proposed by ' . Auth::user() -> username .' ---';
    $liaison_request_message -> save();

    return $liaison_request_message;
  }

}