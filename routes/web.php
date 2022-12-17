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


Route::get('/download', function(Request $request) {
    $filename = 'file-name.docx';
    $tempImage = tempnam(sys_get_temp_dir(), $filename);
    copy('http://localhost/amnesia.docx', $tempImage);
    
    return response()->download($tempImage, $filename);
});

Route::get('/mail-gun', function(Request $request){
    \Mail::to('denniswanjohi.m@gmail.com')->send(new \App\Mail\MailGun());
    return "Sent";
});

Route::get('/', function () {
    return view('welcome');
});

