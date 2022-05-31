<?php

namespace App\Services\Task;

use Illuminate\Support\Facades\DB;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskFetchingService{
  public function AllMine(Type $var = null)
  {
    $tasks = Task::query() -> where('broker_id', Auth::user() -> id) -> get();
   
    // add file urls to each of the tasks
    foreach ($tasks as $task){
        $task -> Files;
    }

    return $tasks;
  }
}