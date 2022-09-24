<?php

namespace App\Services\Statistics;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Task;

class StatisticsService{
    public function getAboutStatistics(){
        $tasks_completed = Task::where('status', '>', 3) -> where('status', '!=', 4) -> count();
        $words_written = Task::where('pages', '>', 0) -> where('status', '>', 2)-> where('status', '!=', 4)  ->  where('status', '!=', 4) -> sum('pages') * 275;
        return [
            'tasks_completed' => $tasks_completed,
            'words_written' => $words_written
        ];
    }
}
