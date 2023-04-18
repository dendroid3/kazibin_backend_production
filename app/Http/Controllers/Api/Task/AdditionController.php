<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Offer\OfferService;
use App\Services\Task\AdditionService;
use App\Services\Telegram\BroadcastService;
use App\Services\SystemLog\LogCreationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class AdditionController extends Controller
{
    public function index(Request $request, AdditionService $addition_service){
        //initialise task upload

        $response = $addition_service -> addInitialTaskDetails($request);

        if(!$response['validated']){
            return  response() -> json([
                'errors' => $response['errors']
            ], 201);
        };
        
        return  response() -> json([
            'task' => $response['task']
        ]);

    }

    public function stepTwo(Request $request, AdditionService $addition_service){
        //add files to task

        return response() -> json([
            'task_files' => $addition_service -> addTaskFiles($request)
        ]);

    }

    public function stepThree(Request $request, AdditionService $addition_service){
        //about page count

        return response() -> json([
            'task' => $addition_service -> addPageCount($request)
        ]);

    }

    public function stepFour(Request $request, AdditionService $addition_service){
        //this step adds the deadline for the task. This record shall be the ine used to give the writer and broker reminders

        return response() -> json([
            'task' => $addition_service -> addDeadline($request)
        ]);
    }

    public function stepFive(Request $request, AdditionService $addition_service){
        /*
            this step adds the payment mode for the task. This record will then be used to give the writer and broker reminders as well. 
                Date 28/05/1965 means payment on delivery
                Date 17/09/1997 means payment on approval
        */

        return response() -> json([
            'task' => $addition_service -> addPayInformation($request)
        ]);
    }

    public function stepSix(
        Request $request, 
        AdditionService $addition_service, 
        BroadcastService $broadcast, 
        OfferService $offer_service, 
        LogCreationService $log_service
    ){
        /*
            This step adds the dificulty of the task as assumed by the broker.
            It also describes who should take the task. If the task is offered to public then the 'takers' field is left null
            If the task is offered to one or more writers then the field houses the ids of the writers separated by the underscore character '_'. All writers in the 'takers' field get 
            an offer.
        */
        
        $response = $addition_service -> addDifficultyAndTakers($request, $offer_service, $log_service);

        if(!$response['validated']){
            return  response() -> json([
                'errors' => $response['errors']
            ], 201);
        }

 
        if($request -> broadcast_on_telegram){
            /*
                Broadcasts on the telegram channel: https://t.me/+DQlirEBXwUMxYWFk
            */
            $transaction = new Transaction;
            $transaction -> user_id = Auth::user() -> id;
            $transaction -> type = "Credit";
            $transaction -> task_id = $response['task'] -> id;
            $transaction -> description = 'Amount charged to broadcast task topic '.  $response['task'] -> topic . ". Code: " .  $response['task'] -> code . " to telegram.";
            $transaction -> amount = 20;
            $transaction -> save();

            $broadcast -> prepareforBroadcasting($response['task']);
        }

        return response() -> json(
            $response['message']
        ); 
    }

    public function deleteTask(
        Request $request,
        AdditionService $addition_service, 
        LogCreationService $log_service
    )
    {
        return response() -> json([
            'message' => $addition_service -> deleteTask($request, $log_service)
        ]);
    }

    

    public function changeDeadline(Request $request, AdditionService $addition_service){
        /*
            this step adds the payment mode for the task. This record will then be used to give the writer and broker reminders as well. 
                Date 28/05/1965 means payment on delivery
                Date 17/09/1997 means payment on approval
        */

        return response() -> json($addition_service -> changeDeadline($request));
    }

}
