<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        array_push($brokers_refined, $broker_refined);
    }
    
    return $brokers_refined;
  }
}