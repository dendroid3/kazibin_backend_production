<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\VerificationEmail;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;
use App\Models\Broker;
use App\Models\Task;
use App\Models\Taskfile;
use App\Models\Bid;
use App\Events\BidMade;
use App\Events\Loginfor;
use App\Events\Taskoffermessage;
use Illuminate\Support\Facades\Log;

Route::get('{any}', function () {
    return response() -> json(['error' => 'forbidden'], 403);
});

