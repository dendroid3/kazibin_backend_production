<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Services\Task\TaskFetchingService;

class FetchController extends Controller
{
    public function getAllPostedByMe(TaskFetchingService $fetch_service){

        return response() -> json(
            $fetch_service -> AllMine()
        );

    }

    public function getAllPostedByMePaginated(Request $request, TaskFetchingService $fetch_service){

        return response() -> json(
            $fetch_service -> AllMinePaginated($request)
        );

    }

    public function getAllDoneByMeFromBrokerForCreatingInvoice(Request $request, TaskFetchingService $fetch_service){
        
        return response() -> json(
            $fetch_service -> getAllDoneByMeFromBrokerForCreatingInvoice($request)
        );

    }

    public function getAllDoneByWriterForCreatingInvoice(Request $request, TaskFetchingService $fetch_service){
        
        return response() -> json(
            $fetch_service -> getAllDoneByWriterForCreatingInvoice($request)
        );

    }

    public function getAllDoneByMe(TaskFetchingService $fetch_service){
        
        return response() -> json(
            $fetch_service -> getAllDoneByMe()
        );
        
    }

    public function getAllDoneByMePaginated(Request $request, TaskFetchingService $fetch_service){
        
        return response() -> json(
            $fetch_service -> getAllDoneByMePaginated($request)
        );

    }

    public function getAllAvailableForBiddingLanding(Request $request){
        $tasks = Task::query() -> where([
            ['takers', '=', null],
            ['status', '=', 1],
            ['full_pay', '!=', null],
            ['expiry_time', '!=', null]
        ]) 
        -> orderBy('expiry_time', 'asc')
        -> select('code', 'unit', 'topic', 'instructions', 'expiry_time', 'full_pay')
        -> take(10)
        -> get();

        foreach ($tasks as $task) {
            // $task -> expiry_time = Carbon::createFromFormat('Y-m-d H:M:S', '1975-05-21 22')->toDateTimeString();
            $task -> full_pay = $task -> full_pay;
        }

        return response() -> json(
            $tasks
        );
    }

    public function getAllAvailableForBidding(Request $request){
        $query = $this -> sortFilterQuery($request -> all());
      
        /*
            Will implement pagination + make "orderBy" toogle between asc and dsc as specified by user 
        */

        
        $mine = Auth::user() -> broker -> tasks -> pluck('id');
        $bidded = Auth::user() -> writer -> bids -> pluck('task_id');

        $tasks = Task::query() -> where($query) 
        -> orderBy('expiry_time', 'asc')
        -> whereNotIn('id', $mine)
        -> whereNotIn('id', $bidded)

        -> take(10)
        -> get();
        // $tasks = DB::table('tasks') -> where($query) -> orderBy('expiry_time', 'asc') -> get();
        foreach ($tasks as $task) {
            // Broker::find($task->broker_id);
            $task -> broker -> user;
        }

        return response() -> json(
            $tasks
        );
    }

    public function getAllAvailableForBiddingPaginated(Request $request, TaskFetchingService $fetch_service)
    {
        return response() -> json(
            $fetch_service -> getAllAvailableForBiddingPaginated($request)
        );
    }

    public function getPublicTask(){
        /*
            This will take a task that is unassigned and available to the public and display all the details about it.
            It is pulled when a user is viewing the specific task for bidding
        */ 

    }

    public function getMyTask(){
        /*
            This will be called by the broker once he clicks on his task to view it.
            It should provide a summary of the task. i.e Logs, Bids, Files and instructions.
            We may include analytics such as number of views in the future.
        */
    }

    public function getAvailabilityDetails(){
        /*
            This function helps populate the filter options. 
            It ensures no filter results come back null.
        */
        $tasks = DB::table('tasks') -> where([
            ['status', '=', '1'],
            ['takers', '=', null],
        ]);
        $units = $tasks -> select('unit') -> distinct() -> get();
        $types = $tasks -> select('type') -> distinct() -> get();
        $max_full_pay = $tasks -> orderBy('full_pay', 'desc') -> select('full_pay') -> first();
        $min_full_pay = $tasks -> orderBy('full_pay', 'asc') -> select('full_pay') -> first();
        return response() -> json([
            'units' => $units,
            'types' => $types,
            'max_full_pay' => $max_full_pay,
            'min_full_pay' => $min_full_pay,
        ]);

    }

    public function getTaskForBidding(Request $request, TaskFetchingService $fetch_service){
        return response() -> json(
            $fetch_service -> getTaskForBidding($request)
        );
    }
    
    public function sortFilterQuery($request){
        /*
            Filtering logic:
            Start with empty query so as to inject the conditions as specified by the filters on the request.
        */ 
        $query = array();

        /*
            tasks of status == 1 are unassigned yet.
        */
        array_push($query, ['status', '=', 1]);

        /*
            tasks with null for in field 'takers' are not offered to any writer, thus, available for bidding
        */
        array_push($query, ['takers', '=', null]);

        /*
            The following are optional filters to sort through tasks
        */
        // if(!$request['type']){
        //     $type_filter = ['type', '=', $request['type']];
        //     array_push($query, $type_filter);
        // }

        // if($request['min_full_pay']){
        //     $min_full_pay_filter = ['full_pay', '>=', $request['min_full_pay']];
        //     array_push($query, $min_full_pay_filter);
        // };

        // if($request['max_full_pay']){
        //     $max_full_pay_filter = ['full_pay', '<=', $request['max_full_pay']];
        //     array_push($query, $max_full_pay_filter);
        // }

        // if($request['unit']){
        //     $unit_filter = ['unit', '=', $request['unit']];
        //     array_push($query, $unit_filter);
        // }

        // if($request['max_pay_day']){
        //     $max_pay_day_filter = ['pay_day', '<=', $request['max_pay_day']];
        //     array_push($query, $max_pay_day_filter);
        // }

        return $query;
    }
}







