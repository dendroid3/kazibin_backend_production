<?php

namespace App\Services\Task;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Task;
use App\Models\Taskfile;
use App\Models\Taskoffer;

class AdditionService {

  public function stepOne(Request $request){
    
    $validator = Validator::make($request->all(), [
      'topic' => ['required', 'min:5', 'bail'],
      'unit' => ['required', 'bail'],
      'type' => ['required', 'bail'],
      'instructions' => ['required', 'min:10', 'bail'],
    ]);

    if ($validator->fails()) {
      return $validator -> errors();
    }

    return ['validated' => true];

  }

}
