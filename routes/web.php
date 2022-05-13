<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\VerificationEmail;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\User;
use App\Models\Task;
use App\Models\Taskfile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
    Route::get('/view/{id}', function ($id) {
        $task = Task::find($id);
        $files = $task ->Files;
        $broker = User::find($task -> broker_id);
        // dd($broker -> id);
        return view('task') -> with('task',$task)
                            -> with('files', $files) 
                            -> with('broker', $broker);
        echo $task;
    });

    /*
        These are test URLs disregard them
    */

    Route::get('/', function () {
        // dd(env('QUEUE_CONNECTION'));
        // echo Str::orderedUuid() -> toString();
        // $activity = Telegram::getUpdates();
        // dd($activity);

        // -1001693325642
        $text = "A new contact us query\n"
        . "<b>Email Address: </b>\n"
        . "Denniswanjohi.m@live.com\n"
        . "<b>Message: </b>\n";

        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001693325642'),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);

        // echo Carbon::now()->toDateTimeString();
    });

    Route::get('/login', function () {
        echo('log in');
    }) -> name('login');

    Route::get('/sms', function () {
        $user = User::find(5);
        $text = 'Hello ' . $user -> username . 
        '. ProMIS verification code: ' .
        strtoupper($user -> phone_verification) . '. It is valid for 30 minutes.';
        Nexmo::message() -> send([
            'to'=> '254797727253',
            'from'=>'254797727253',
            'text'=> $text
        ]);
    });

    Route::get('/mail', function () {
        $user = 'i am user';
        // Mail::to('denniswanjohi.m@live.com')->send(new VerificationEmail($user));
        return new VerificationEmail(User::find(1));
    });
