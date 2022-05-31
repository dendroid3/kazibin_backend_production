<?php

namespace App\Services\SystemLog;

use App\Models\Log;

class LogCreationService 
{
  public function createTaskLog($task){
    // Creates a log entry associated with tasks
    $stub_1 = 'Job, topic: ';
    $stub_2 = $task['topic'];
    $stub_3 = ', added successfully and given code: ';
    $stub_4 = $task['code'];
    $stub_5 = '. It will be offered to ';
    if($task -> takers) {
        $stub_6 = 'the ' . count(explode('_', $task['takers'])) . ' writers you selected.';
    } else {
        $stub_6 = 'all writers to bid on.';
    }
    $message = $stub_1 . $stub_2 . $stub_3 . $stub_4 . $stub_5 . $stub_6;

    $log = new Log;
    $log -> user_id = $task['broker_id'];
    $log -> foreign_id = $task['id'];
    $log -> code = 'task';
    $log -> message = $message;
    $log -> save();
  }

  public function createOfferLog($offer, $task){
    // Creates a log entry associated with offers
    $stub_1 = 'You have been offered a job, topic: ';
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
    $log -> code = 'offer';
    $log -> message = $message;
    $log -> save();
  }

}