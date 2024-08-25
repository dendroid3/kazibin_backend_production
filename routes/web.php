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

use App\Mail\VerficationOfAccount;


Route::match(['get', 'post'], '/botman', function() {
    $botman = app('botman');

    $botman->listen();
});


Route::get('/registration_email', function () {
    $user = User::query() 
    // -> where('email_verification', '!=', null) 
    -> orderBy('created_at', 'desc') -> first(); 

    return new VerficationOfAccount($user);
});

Route::get('/_t', function () {
    // Response is an array of updates.
    // $updates = \NotificationChannels\Telegram\TelegramUpdates::create()
        // (Optional). Get's the latest update. NOTE: All previous updates will be forgotten using this method.
        // ->latest()

        // (Optional). Limit to 2 updates (By default, updates starting with the earliest unconfirmed update are returned).
        // ->limit(2)

        // (Optional). Add more params to the request.
    //     ->options([
    //         'timeout' => 0,
    //     ])
    //     ->get();

    // dd($updates);
    $response = Telegram::sendMessage([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'text' => 'Hello World'
    ]);
    dd($response -> getMessageId());
})->name('test');

Route::get('/webhook', function () {
    $response = Telegram::setWebhook(['url' => 'https://example.com/<token>/webhook']);

    # Or if you are supplying a self-signed-certificate
    $response = Telegram::setWebhook([
        'url' => env('TELEGRAM_WEBHOOK_URL') . '/webhook_res',
        // 'certificate' => '/path/to/public_key_certificate.pub'
    ]);

    dd($response);
});

Route::post('/webhook_res', function ($request) {
    Log::info($request -> all());
});

Route::post('/webhooked', function (Request $request) {
    // dd(Telegram::commandsHandler(true));
    // dd(Telegram::getWebhookUpdate());
    Log::info($request);
    Log::info("Called");
    // dd(Telegram::addCommand(App\TelegramCommands\startCommand::class));
    return response() -> json(['error' => 'forbidden'], 403);
});
Route::get('{any}', function () {
    // dd(Telegram::commandsHandler(true));
    // dd(Telegram::getWebhookUpdate());
    Log::info($request);
    Log::info("Called");
    // dd(Telegram::addCommand(App\TelegramCommands\startCommand::class));
    return response() -> json(['error' => 'forbidden'], 403);
});

