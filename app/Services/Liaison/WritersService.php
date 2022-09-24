<?php

namespace App\Services\Liaison;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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
        $writer_refined = $writer -> user() -> select('id', 'username', 'email', 'phone_number', 'availabile', 'code') -> first();
        $writer_refined -> writer_id= $writer -> id; 
        array_push($writers_refined, $writer_refined);
    }

    return $writers_refined;
  }
}