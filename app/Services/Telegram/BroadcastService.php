<?php

namespace App\Services\Telegram;

use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\Carbon;

class BroadcastService {
  public function braodcastToTaskChannel($text){
    Telegram::sendMessage([
      'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001693325642'),
      'parse_mode' => 'HTML',
      'text' => $text
    ]);
  }
  
  public function braodcastToErrorChannel($error){
    $text = "<u><b> Error Code: " . $error['error_code'] . "</b></u>\n" .
    "Error Message: " . $error['message'] . "\n" .
    "User Phone Number: " . $error['user_phone_number'];
    Telegram::sendMessage([
      'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001693325642'),
      'parse_mode' => 'HTML',
      'text' => $text
    ]);
  }

  public function prepareForBroadcasting($task){
    // dd($task);
    $text = "<u><b>" . $task->code .": </b>" 
    . "<b>" . $task->unit . " " . $task->type . "</b></u> \n \n"
    . "Due on: <b>"
    . $this -> checkDueTimeOn($task -> expiry_time) . "</b> \n"
    . "Time left: <b>"
    . $this -> checkDueTimeIn($task -> expiry_time) . "</b> \n \n"
    . "Payment: <b>"
    . $this -> checkPayDay($task -> pay_day) . ". </b> \n"
    . ($task -> pages ?  "Pages: <b>" . $task -> pages . "</b> \n" : '')
    . ($task -> pages ?  "Cost Per Page: <b>" . $task -> page_cost . "</b> \n" : '')
    . "Amount: "
    . "<b>" . $task->full_pay . "</b> \n  \n"
    //change this url to the one you are serving this app on + "/view/"
    . "http://192.168.0.103:8080/t/" 
    . $task -> code;

    $this -> braodcastToTaskChannel($text);
  }

  public function checkDueTimeOn($date){
    // formats deadline to human readable formart
    return Carbon::create($date) -> format('M jS')  . ' at ' . Carbon::create($date) -> format('g:i');
  }

  public function checkDueTimeIn($date){
      //Shows time to deadline of task
      $time = CarbonInterval::make(
          Carbon::parse(Carbon::now()  -> format('Y-m-d g:i'))
          ->diff(Carbon::parse(Carbon::create($date) -> format('Y-m-d g:i')))
      ) -> forHumans();
      return $time;
  }

  public function checkPayDay($date){
      // Renders the payment mode with accordance to the comment on the function 'stepFive'
      if($date == '1997-09-17 00:00:00'){
          return 'On Approval';
      } else if($date == '1965-05-28 00:00:00'){
          return 'On Delivery';
      } else {
          return Carbon::create($date) -> isoFormat('MMMM Do YYYY');
      }

  }

}
