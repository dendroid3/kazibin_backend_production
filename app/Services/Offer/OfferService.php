<?php

namespace App\Services\Offer;
use App\Services\SystemLog\LogCreationService;


use App\Models\Taskoffer;

class OfferService
{
  public function create($task, LogCreationService $log_service, $taker)
  {
    $offer = new Taskoffer();
    $offer -> task_id = $task -> id;
    $offer -> writer_id = $taker;
    $offer -> save();

    $log_service -> createOfferLog($offer, $task);

    return ['validated' => true, 'created' => true];
  }
}