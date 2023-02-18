<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Broker;

class BrokersService {
  public function getAll(){
    $brokers = User::query()
          -> where('id', '!=', Auth::user() -> id)
          -> orderBy('broker_score', 'DESC')
          -> take(10)
          -> get();
    // ? DB::table('users') -> get();
    foreach ($brokers as $broker) {
      $broker -> broker;
      $broker -> writers = count($broker -> broker -> writers);
      $broker -> total_tasks =  count($broker -> broker -> tasks);
      $broker -> available_tasks = count($broker -> broker -> tasks -> where('status', 1));
      $broker -> cancelled_tasks =  count($broker -> broker -> tasks -> where('status', 4));
    }
    return $brokers;
  }
  
  public function getAllPaginated(){
    $brokers = User::query()
          -> where('id', '!=', Auth::user() -> id)
          -> orderBy('broker_score', 'DESC')
          -> paginate(10);
          Log::info('called');
    // ? DB::table('users') -> get();
    foreach ($brokers as $broker) {
      $broker -> broker;
      $broker -> writers = count($broker -> broker -> writers);
      $broker -> total_tasks =  count($broker -> broker -> tasks);
      $broker -> available_tasks = count($broker -> broker -> tasks -> where('status', 1));
      $broker -> cancelled_tasks =  count($broker -> broker -> tasks -> where('status', 4));
    }
    return $brokers;
  }

  public function getMyBrokers(){
    $brokers = Auth::user() -> writer -> brokers() -> paginate(10);
    
    $brokers_refined = array();
    
    foreach ($brokers as $broker) {
        $broker_refined = Broker::find($broker -> pivot -> broker_id) -> user;
        $broker_refined -> broker_id = $broker -> id;
        
        $tasks = $broker -> tasks -> where('writer_id', Auth::user() -> writer -> id) -> all();
        $broker_refined -> tasks_done = count($tasks);
        $broker_refined -> total_amount = 0;

        $broker_refined -> underway = 0;
        $broker_refined -> complete = 0;
        $broker_refined -> cancelled = 0;
        $broker_refined -> paid = 0;
        
        foreach ($tasks as $task) {
          if($task -> status == 2){
            $broker_refined -> total_amount =+ $task -> full_pay;
            $broker_refined -> underway++;
          } else if($task -> status == 3){
            $broker_refined -> total_amount =+ $task -> full_pay;
            $broker_refined -> complete++;
          } else if($task -> status == 4){
            $broker_refined -> cancelled++;
          }else if(($task -> status == 6) || ($task -> status == 8)){
            $broker_refined -> total_amount =+ $task -> full_pay;
            $broker_refined -> paid++;
          }
        }
        array_push($brokers_refined, $broker_refined);

    }
    
    return $brokers_refined;
  }

  public function getOneBroker(Request $request){
    $broker = User::query() -> where('code', $request -> code) -> first();
    $broker -> broker;
    $broker -> writers = count($broker -> broker -> writers);
    $broker -> total_tasks =  count($broker -> broker -> tasks);
    $broker -> available_tasks = count($broker -> broker -> tasks -> where('status', 1));
    $broker -> cancelled_tasks =  count($broker -> broker -> tasks -> where('status', 4));

    return $broker;
  }
}