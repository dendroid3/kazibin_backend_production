<?php

namespace App\Services\Updates;

use App\Models\Task;

class UpdatesService{

  public function updateTask($task_id, $time){

    $task = Task::find($task_id);
    $task -> updated_at = $time;
    $task -> push();
    
  }

}
