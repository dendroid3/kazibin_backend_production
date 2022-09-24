<?php

namespace App\Http\Controllers\Api\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Statistics\StatisticsService;

class StatisticsController extends Controller
{
    public function getAboutStatistics(StatisticsService $statistics_service){
        return response() -> json(
            $statistics_service -> getAboutStatistics()
        );
    }
}
