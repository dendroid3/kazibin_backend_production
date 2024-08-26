<?php

namespace App\Services\Telegram;

use Telegram\Bot\Laravel\Facades\Telegram;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BroadcastService {
  public function braodcastToTaskChannel($text){
    Telegram::sendMessage([
      'chat_id' => env('TELEGRAM_CHANNEL_ID'),
      'parse_mode' => 'HTML',
      'text' => $text
    ]);
  }
  
  public function braodcastToErrorChannel($error){
    $text = "<u><b> Error Code: " . $error['error_code'] . "</b></u>\n" .
    "Error Message: " . $error['message'] . "\n" .
    "User Phone Number: " . $error['user_phone_number'];
    Telegram::sendMessage([
      'chat_id' => env('TELEGRAM_CHANNEL_ID'),
      'thread_id' => 3,
      'parse_mode' => 'HTML',
      'text' => $text
    ]);
  }

  public function broadcatToWhatsAppGroup($task) {
    $task_url = env('APP_CLIENT', "https://app.kazibin.com") 
    . "/t/"
    . $task -> code;

    $text = "*" . $task->code .": " 
    . $task->unit . " " . strtoupper($task->type) . "* -- --"
    . "Due on: *" 
    . $this -> checkDueTimeOn($task -> expiry_time) . "* --"
    . "Time left: *"
    . $this -> checkDueTimeIn($task -> expiry_time) . "* -- --"
    . "Payment: *"
    . $this -> checkPayDay($task -> pay_day) . ".* --"
    . ($task -> pages ?  "Pages: *" . $task -> pages . "* --" : '')
    . ($task -> pages ?  "Cost Per Page: *" . $task -> page_cost . "* --" : '')
    . "Amount: "
    . "*" . $task->full_pay . "*--  --"
    . $task_url;

    $command = 'whatsapp:send "' . $text . '"';
    Log::info($command);

    Artisan::call($command);

    // $client = new Client();

    // // Define the URL
    // $url = 'https://api.ultramsg.com/instance93084/messages/chat';

    // // Define the parameters to be sent in the POST request
    // $params = [
    //     'token' => 'krqt75rr7gzh0bhl',
    //     'to'    => '120363328427282938@g.us',
    //     'body'  => $text
    // ];

    // // Send the POST request
    // $response = $client->post($url, [
    //     'headers' => [
    //         'Content-Type' => 'application/x-www-form-urlencoded',
    //     ],
    //     'form_params' => $params,
    // ]);

  }

  public function prepareForBroadcasting($task){
    $task_url = env('APP_CLIENT', "https://app.kazibin.com") 
    . "/t/"
    . $task -> code;

    $text = "<u><b>" . $task->code .": </b>" 
    . "<b>" . $task->unit . " " . strtoupper($task->type) . "</b></u> \n \n"
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
    . '<a href="' . $task_url. '">View Task</a>';

    // $this -> braodcastToTaskChannel($text);
    $this -> broadcatToWhatsAppGroup($task);
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
