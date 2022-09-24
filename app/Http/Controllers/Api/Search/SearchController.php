<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;

class SearchController extends Controller
{
    public function searchFromHome(Request $request)
    {
        $user = User::where('code', $request['code']) -> first();

        if($user){
            $user -> total_tasks_posted =  count($user -> broker -> tasks);
            $user -> writers_count = count($user -> broker -> writers);

            $user -> total_tasks_taken = count($user -> writer -> tasks);
            $user -> brokers_count = count($user -> writer -> brokers);
        }

        $task = Task::where('code', $request['code']) -> first();

        if($task){
            $task -> broker -> user;
            $task -> files;
        }

        // $task = 

        return response() -> json([
            'user' => $user,
            'task' => $task
        ]);
    }
}
