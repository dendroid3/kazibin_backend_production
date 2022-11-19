<?php

namespace App\Services\SystemLog;

use Illuminate\Support\Facades\DB;

use App\Models\Log;
use App\Models\Writer;
use App\Models\Broker;

class LogCreationService 
{
  public function createSystemMessage($user_id, $message, $foreign_id, $code){
    $log = new Log;
    $log -> user_id = $user_id;
    $log -> message = $message;
    $log -> foreign_id = $foreign_id;
    $log -> code = $code;
    $log -> save();
    return $log;
  }

  public function createTaskLog($task){
    // Creates a log entry associated with tasks
    $stub_1 = 'Task, topic: ';
    $stub_2 = $task['topic'];
    $stub_3 = ', added successfully and given code: ';
    $stub_4 = $task['code'];
    $stub_5 = '. It will be offered to ';
    if($task -> takers) {
        $stub_6 = 'the ' . (count(explode('_', $task['takers'])) - 1). ' writers you selected.';
    } else {
        $stub_6 = 'all writers to bid on.';
    }
    $message = $stub_1 . $stub_2 . $stub_3 . $stub_4 . $stub_5 . $stub_6;

    $log = new Log;
    $log -> user_id = $task['broker_id'];
    $log -> foreign_id = $task['id'];
    $log -> code = 'Task Created';
    $log -> message = $message;
    $log -> save();

    return $message;
  }

  public function createOfferLog($offer, $task){
    // Creates a log entry associated with offers
    $stub_1 = 'You have been offered a task, topic: ';
    $stub_2 = $task['topic'];
    $stub_3 = ', code: ';
    $stub_4 = $task['code'];
    $stub_5 = '. It pays: ';
    $stub_6 = $task['full_pay'];

    if($task['pages']){
        $stub_7 = '/= for ' . $task['pages'] . ' pages';
    } else {
        $stub_7 = '/= for the whole task';
    }


    $stub_8 = '. Task due by: ';
    $stub_9 = $task['expiry_time'];

    $message = $stub_1 . $stub_2 . $stub_3 . $stub_4 . $stub_5 . $stub_6 . $stub_7 . $stub_8 . $stub_9;

    $log = new Log;
    $log -> user_id = $offer['writer_id'];
    $log -> other_user_id = $task['broker_id'];
    $log -> foreign_id = $offer['id'];
    $log -> code = 'Offer Recieved';
    $log -> message = $message;
    $log -> save();

    return $message;
  }

  public function createLogOnRequestToWriter($liaison_request, $initiator){
    $broker_log_stub_1 = 'Request sent to writer. Username: ';
    $other_party = Writer::find($liaison_request -> writer_id) -> user;
    #DB::table('users') -> where('id', $liaison_request -> writer_id) -> first();
    $broker_log_stub_2 = $other_party -> username;
    $broker_log_stub_3 = '. Code: ';
    $broker_log_stub_4 = $other_party -> code;

    $broker_message = $broker_log_stub_1 . $broker_log_stub_2 . $broker_log_stub_3 . $broker_log_stub_4;
    $user_id_for_writer = Writer::find($liaison_request['writer_id']) -> user -> id;
    $log = new Log;
    $log -> user_id = $initiator['id'];
    $log -> foreign_id = $liaison_request['id'];
    $log -> other_user_id = $user_id_for_writer;
    $log -> code = 'Request Sent';
    $log -> message = $broker_message;
    $log -> save();

    
    $writer_log_stub_1 = 'Request recieved from broker. Username: ';
    $writer_log_stub_2 = $initiator['username'];
    $log -> other_user_id = $liaison_request['broker_id'];
    $writer_log_stub_3 = '. Code: ';
    $writer_log_stub_4 = $initiator['code'];
    $writer_log_stub_5 = '. Accept to have the user as your task broker to be to have them send you task offers.';

    $writer_message = $writer_log_stub_1 . $writer_log_stub_2 . $writer_log_stub_3 . $writer_log_stub_4 . $writer_log_stub_5;

    $log = new Log;
    $log -> user_id = $user_id_for_writer;
    $log -> foreign_id = $liaison_request['id'];
    $log -> other_user_id = $initiator['id'];
    $log -> code = 'Request Recieved';
    $log -> message = $writer_message;
    $log -> save();
  }

  public function createLogOnRequestToBroker($liaison_request, $initiator){
    $writer_log_stub_1 = 'Request sent to broker. Username: ';
    $other_party = Broker::find($liaison_request -> broker_id) -> user;
    #DB::table('users') -> where('id', $liaison_request -> broker_id) -> first();
    $writer_log_stub_2 = $other_party -> username;
    $writer_log_stub_3 = '. Code: ';
    $writer_log_stub_4 = $other_party -> code;

    $writer_message = $writer_log_stub_1 . $writer_log_stub_2 . $writer_log_stub_3 . $writer_log_stub_4;

    $user_id_for_broker = Broker::find($liaison_request['broker_id']) -> user -> id;

    $log = new Log;
    $log -> user_id = $initiator['id'];
    $log -> foreign_id = $liaison_request['id'];
    $log -> other_user_id = $user_id_for_broker;
    $log -> code = 'Request Sent';
    $log -> message = $writer_message;
    $log -> save();

    
    $broker_log_stub_1 = 'Request recieved from writer. Username: ';
    $broker_log_stub_2 = $initiator['username'];
    $broker_log_stub_3 = '. Code: ';
    $broker_log_stub_4 = $initiator['code'];
    $broker_log_stub_5 = '. Accept to have the user as your writer to be able to send them task offers.';

    $broker_message = $broker_log_stub_1 . $broker_log_stub_2 . $broker_log_stub_3 . $broker_log_stub_4 . $broker_log_stub_5;

    $log = new Log;
    $log -> user_id = $user_id_for_broker;
    $log -> foreign_id = $liaison_request['id'];
    $log -> other_user_id = $liaison_request['initiator_id'];
    $log -> code = 'Request Received';
    $log -> message = $broker_message;
    $log -> save();
  }

}