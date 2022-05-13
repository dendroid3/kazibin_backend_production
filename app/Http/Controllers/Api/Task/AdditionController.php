<?php

namespace App\Http\Controllers\Api\Task;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\Carbon;

use App\Models\Task;
use App\Models\Taskfile;
use App\Models\Taskoffer;
use App\Models\Log;


class AdditionController extends Controller
{
    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'topic' => ['required', 'min:5', 'bail'],
            'unit' => ['required', 'bail'],
            'type' => ['required', 'bail'],
            'instructions' => ['required', 'min:10', 'bail'],
        ]);

        if ($validator->fails()) {
            return  response() -> json([
                'errors' => $validator -> errors()
            ], 201);
        }
        
        $task = new Task;
        $task -> broker_id = Auth::user() -> id;
        $task -> status = 1;
        $task -> topic = $request -> topic;
        $task -> unit = $request -> unit;
        $task -> type = $request -> type;
        $task -> instructions = $request -> instructions;
        $task -> save();

        $task -> code = strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(rand(3,7))); 

        $task -> push();
        return  response() -> json([
            'task' => $task
        ], 200);
    }

    public function stepTwo(Request $request){
        //add files to task
        $files = $request -> file('documents');
        $file_urls = array();
        $i = 0;
        foreach ($files as $file) {
            $request->file('documents')[$i]->store('public');

            $task_file = new Taskfile;
            $task_file -> task_id = $request -> task_id;
            $task_file -> url = $request -> file('documents')[$i] -> hashName();
            $task_file -> name =  $request -> file('documents')[$i] -> getClientOriginalName();
            $task_file -> save();

            array_push($file_urls, $task_file);
            $i++;
        }

        return response() -> json([
            'task_files' => $file_urls
        ]);
    }

    public function stepThree(Request $request){
        //about page count
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

        return response() -> json([
            'task' => $task
        ]);

    }

    public function stepFour(Request $request){
        //this step adds the deadline for the task. This record shall be the ine used to give the writer and broker reminders
        $task = Task::find($request -> task_id);
        $task -> expiry_time = $request -> expiry_time;
        $task -> push();

        return response() -> json([
            'task' => $task
        ]);
    }

    public function stepFive(Request $request){
        /*
            this step adds the payment mode for the task. This record shall be the ine used to give the writer and broker reminders as well. 
                Date 28/05/1965 means payment on delivery
                Date 17/09/1997 means payment on approval
        */
        $task = Task::find($request -> task_id);
        $task -> pay_day = $request -> pay_day;
        $task -> push();

        return response() -> json([
            'task' => $task
        ]);
    }

    public function stepSix(Request $request){
        /*
            This step adds the dificulty of the task as assumed by the broker.
            It also describes who should take the task. If the task is offered to public then the 'takers' field is left null
            If the task is offered to one or more writers then the field houses the ids of the writers separated by the underscore character '_'. All writers in the 'takers' field get 
            an offer.
        */
        $validator = Validator::make($request->all(), [
            'difficulty' => ['required', 'bail'],
        ]);
        
        if ($validator->fails()) {
            return  response() -> json([
                'errors' => $validator -> errors()
            ], 201);
        }

        $task = Task::find($request -> task_id);
        $task -> takers = $request -> takers;
        $task -> difficulty = $request -> difficulty;
        $task -> push();

        if($request -> broadcast_on_telegram == false){
            $takers = explode('_', $task -> takers);
            foreach ($takers as $taker) {
                if($taker){
                    $offer = new Taskoffer();
                    $offer -> task_id = $task -> id;
                    $offer -> writer_id = $taker;
                    $offer -> save();

                    $this -> createOfferLog($offer, $task);
                }
            }
        } else {
            $this -> broadCastOnTelegram($task);
        }

        $this -> createTaskLog($task);
        return response() -> json([
            'task' => $task
        ]);
    }

    public function broadCastOnTelegram($task){
        /*
            Broadcasts on the telegram channel: https://t.me/+DQlirEBXwUMxYWFk
        */
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
        . "http://192.168.0.101:8000/view/"
        . $task -> id;

        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001693325642'),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
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
        $log -> foreign_id = $offer['id'];
        $log -> code = 'offer';
        $log -> message = $message;
        $log -> save();
    }

}
