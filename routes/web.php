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

// Route::get('/download', [App\Http\Controllers\Api\Download\DownloadController::class, 'download']) -> name('file.download');


Route::get('/download', function(Request $request) {
    $filename = 'file-name.docx';
    $tempImage = tempnam(sys_get_temp_dir(), $filename);
    copy('http://localhost/amnesia.docx', $tempImage);
    
    return response()->download($tempImage, $filename);
    // $offer_message = Taskoffermessage::find($request -> message_id);
                
    // $filename = $offer_message -> message;
    // $tempImage = tempnam(sys_get_temp_dir(), $filename);
    // copy($offer_message -> type, $tempImage);
            
    // return response()->download($tempImage, $filename);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
});
Route::get('/test_broadcast', function () {
    Log::info(Bid::first() -> task -> broker -> user -> id);
    $bid = Bid::first();
    $bid -> writer -> user;
    $bid -> last_message = $bid -> messages() -> orderBy('created_at', 'DESC') -> take(1) -> get();
    // if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', Auth::user() -> id) -> first()){

    if($bid  -> messages -> where('read_at', null)  -> where('user_id', '!=', 1) -> where('user_id', '!=', 22) -> first()){
      $bid -> unread_message = true;
    }

    event(new BidMade($bid, 'it it OK', $bid -> task -> broker -> user -> id));

    dd('OK');

});


    Route::get('/event', function() {
        // dispatch::Loginfor();

        event(new Loginfor());
    });
    
    Route::get('/view/{id}', function ($id) {
        $task = Task::find($id);
        $files = $task ->Files;
        $broker = Broker::find($task -> broker_id) -> user;
        // dd($broker -> id);
        return view('task') -> with('task',$task)
                            -> with('files', $files) 
                            -> with('broker', $broker);
        echo $task;
    });

    /*
        These are test URLs disregard them
    */

    // Route::get('/', function () {
        // dd(env('QUEUE_CONNECTION'));
        // echo Str::orderedUuid() -> toString();
        // $activity = Telegram::getUpdates();
        // dd($activity);

        // -1001693325642
        // $text = "A new contact us query\n"
        // . "<b>Email Address: </b>\n"
        // . "Denniswanjohi.m@live.com\n"
        // . "<b>Message: </b>\n";

        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001693325642'),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);
        // return 'Hello';

        // echo Carbon::now()->toDateTimeString();
    // });

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
