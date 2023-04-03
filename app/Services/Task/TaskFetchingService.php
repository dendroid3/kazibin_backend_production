<?php

namespace App\Services\Task;

use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class TaskFetchingService{
  public function AllMine()
  {
    $tasks = Task::query() 
    -> where('broker_id', Auth::user() -> broker -> id) 
    -> orderBy('updated_at', 'DESC')
    -> take(10)
    -> get();
   
    // add file urls to each of the tasks
    foreach ($tasks as $task){
        $task -> Files;
        $task -> ratings;
        if($task -> writer_id){
          $task -> writer -> user;
        }

        if($task -> status === 1){

          foreach ($task -> offers as $offer) {
            $offer -> writer -> user;

            $offer -> last_message = $offer -> messages() -> orderBy('created_at', 'DESC') -> take(1) -> get();
            if($offer  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $offer -> unread_message = true;
            }
          }
          
          foreach ($task -> bids as $bid) {
            $bid -> writer -> user;
            $bid -> last_message = $bid -> messages() -> orderBy('created_at', 'DESC') -> take(1) -> get();
            if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $bid -> unread_message = true;
            }
          }
        } else {
          if($task  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
            $task -> unread_message = true;
          }
        }

    }

    return $tasks;
  }

  public function AllMinePaginated(Request $request)
  {
    if(!$request -> status){
      $tasks = Task::query() 
      -> where('broker_id', Auth::user() -> broker -> id) 
      -> orderBy('updated_at', 'DESC')
      -> paginate(10);
    } else {
      $tasks = Task::query() 
      -> where('broker_id', Auth::user() -> broker -> id) 
      -> where('status', $request -> status)
      -> orderBy('updated_at', 'DESC')
      -> paginate(10);
    }

    // Log::info($request -> status);
   
    // add file urls to each of the tasks
    foreach ($tasks as $task){
        $task -> ratings;
        $task -> Files;
        if($task -> writer_id){
          $task -> writer -> user;
        }

        if($task -> status === 1){

          foreach ($task -> offers as $offer) {
            // $offer -> messages -> first();
            $offer -> writer -> user;

            $offer -> last_message = $offer -> messages() -> orderBy('created_at', 'DESC') -> take(1) -> get();
            if($offer  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $offer -> unread_message = true;
            }
          }
          
          foreach ($task -> bids as $bid) {
            $bid -> writer -> user;
            $bid -> last_message = $bid -> messages() -> orderBy('created_at', 'DESC') -> take(1) -> get();
            if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
              $bid -> unread_message = true;
            }
          }
        } else {
          if($task  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
            $task -> unread_message = true;
          }
        }

    }

    return $tasks;
  }

  public function getAllDoneByMe()
  {
    // get all the tasks the writer has ever taken
    $tasks = Task::where('writer_id', Auth::user() -> writer -> id) -> take(10) -> orderBy('updated_at', 'DESC') -> get();

    // add file urls to each of the tasks
    foreach ($tasks as $task){
        $task -> files;
        $task -> broker -> user; 
        $task -> Files;
        $task -> ratings;
        if($task  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
          $task -> unread_message = true;
        }
    }

    return $tasks;

    // can get other details on the individual tasks here as the need arises

  }
  
  public function getAllDoneByMeFromBrokerForCreatingInvoice(Request $request)
  {
    $broker_id = User::where('code', $request['code']) -> first() -> broker -> id;
    $tasks = Task::where('writer_id', Auth::user() -> writer -> id) 
        -> where('status', 3)
        -> where('broker_id', $broker_id)
        -> orderBy('created_at', 'ASC')
        -> select('created_at', 'code', 'topic', 'id', 'full_pay', 'broker_id', 'writer_id')
        -> get();

    return $tasks;
  }
  
  public function getAllDoneByWriterForCreatingInvoice(Request $request)
  {
    $writer_id = User::where('code', $request['code']) -> first() -> writer -> id;
    Log::info($writer_id);
    $tasks = Task::where('broker_id', Auth::user() -> broker -> id) 
        -> where('status', 3)
        -> where('writer_id', $writer_id)
        -> take(5)
        -> orderBy('created_at', 'ASC')
        -> select('created_at', 'code', 'topic', 'id', 'full_pay', 'broker_id', 'writer_id')
        -> get();

    return $tasks;
  }
  
  public function getAllDoneByMePaginated(Request $request)
  {
    if(!$request -> status){
      $tasks = Task::query() 
      -> where('writer_id', Auth::user() -> writer -> id) 
      -> orderBy('updated_at', 'DESC')
      -> paginate(10);
    } else {
      $tasks = Task::query() 
      -> where('writer_id', Auth::user() -> writer -> id) 
      -> where('status', $request -> status)
      -> orderBy('updated_at', 'DESC')
      -> paginate(10);
    }

    // add file urls to each of the tasks
    foreach ($tasks as $task){
        $task -> ratings;
        $task -> files;
        $task -> broker -> user; 
        $task -> Files;
        
        if($task  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){
          $task -> unread_message = true;
        }
        // = DB::table('taskfiles') -> where('task_id', $task -> id) -> get();
    }

    return $tasks;

    // can get other details on the individual tasks here as the need arises

  }

  public function getTaskForBidding(Request $request)
  {
    $task = Task::where('code', $request -> task_code) -> first();

    if($task -> status > 1 || !$task){
      return 404;
    } else {
      $task -> files;
      $task -> broker -> user; 
      $task -> Files;

      return $task;
    }

  }

  public function getAllAvailableForBiddingPaginated(Request $request)
  {
    $mine = Auth::user() -> broker -> tasks -> pluck('id');
    $bidded = Auth::user() -> writer -> bids -> pluck('task_id');

    $query = Task::where('status', 1);
    if($request['min_full_pay']){
      $query -> where('full_pay', '>', $request['min_full_pay']);
    } 
    if($request['max_full_pay']){
      $query -> where('full_pay', '<', $request['max_full_pay']);
    } 
    if($request['type']){
      $query -> where('type', $request['type']);
    } 
    if($request['unit']){
      $query -> where('unit', $request['unit']);
    } 

    $query -> orderBy('expiry_time', 'asc')
     
    -> whereNotIn('id', $mine)
    -> whereNotIn('id', $bidded);

    $tasks = $query -> paginate(10);
    foreach ($tasks as $task) {
        $task -> broker -> user;
    }

    return $tasks;

  }

}