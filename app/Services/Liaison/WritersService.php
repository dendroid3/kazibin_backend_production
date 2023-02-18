<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Task;

class WritersService {
  public function getAll(){
    $writers = User::query()
    -> where('id', '!=', Auth::user() -> id)
    -> orderBy('writer_score', 'DESC')
    -> take(10)
    -> get();
    
    foreach ($writers as $writer) {
      $writer -> writer;
      
      $writer -> brokers = count($writer -> writer -> brokers);
      $writer -> total_tasks =  count($writer -> writer -> tasks);
      $writer -> underway_tasks = count($writer -> writer -> tasks -> where('status', 2));
      $writer -> cancelled_tasks =  count($writer -> writer -> tasks -> where('status', 4));
    }

    return $writers;
  }
  
  public function getAllPaginated(){
    $writers = User::query()
    -> where('id', '!=', Auth::user() -> id)
    -> orderBy('writer_score', 'DESC')
    -> paginate(10);
    
    foreach ($writers as $writer) {
      $writer -> writer;
      
      $writer -> brokers = count($writer -> writer -> brokers);
      $writer -> total_tasks =  count($writer -> writer -> tasks);
      $writer -> underway_tasks = count($writer -> writer -> tasks -> where('status', 2));
      $writer -> cancelled_tasks =  count($writer -> writer -> tasks -> where('status', 4));
    }

    return $writers;
  }

  public function getMyWriters(){
    $writers = Auth::user() -> broker -> writers -> take(10);

    $writers_refined = array();

    foreach ($writers as $writer) {
        $writer_refined = $writer -> user() -> select('id', 'username', 'email', 'phone_number', 'availabile', 'code', 'credential_verification') -> first();
        //get interaction details
        $tasks = $writer -> tasks -> where('broker_id', Auth::user() -> broker -> id) -> all();
        $writer_refined -> tasks_done = count($tasks);
        $writer_refined -> total_amount = 0;
        
        $writer_refined -> underway = 0;
        $writer_refined -> complete = 0;
        $writer_refined -> cancelled = 0;
        $writer_refined -> paid = 0;
        
        foreach ($tasks as $task) {
          if($task -> status == 2){
            $writer_refined -> total_amount =+ $task -> full_pay;
            $writer_refined -> underway++;
          } else if($task -> status == 3){
            $writer_refined -> total_amount =+ $task -> full_pay;
            $writer_refined -> complete++;
          } else if($task -> status == 4){
            $writer_refined -> cancelled++;
          }else if(($task -> status == 6) || ($task -> status == 8)){
            $writer_refined -> total_amount =+ $task -> full_pay;
            $writer_refined -> paid++;
          }
        }
        $writer_refined -> writer_id= $writer -> id; 
        array_push($writers_refined, $writer_refined);
    }

    return $writers_refined;
  }

  public function getMyWriter(Request $request){

    $writer_tasks = Task::query() -> where('broker_id', Auth::user() -> broker -> id)
    -> where('writer_id', $request -> writer_id)
    -> select('code', 'created_at', 'difficulty', 'expiry_time', 'full_pay', 'id', 'page_cost', 'pages', 'status', 'topic', 'type', 'unit')
    -> orderBy('updated_at', 'DESC') -> paginate(10);

    return $writer_tasks;
  }
}