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
use GuzzleHttp\Client;

use App\Mail\VerficationOfAccount;
use App\Events\MpesaTransactionComplete;
use App\Events\TaskAdded;

Route::match(['get', 'post'], '/botman', function() {
    $botman = app('botman');

    $botman->listen();
});

Route::get('/event', function() {
    // event(new BidMade('Success', 'Hello', '9cee3126-3546-4d50-ae78-cfb653a15195'));
    $users = User::all();
    $task = Task::first();
    event(new TaskAdded("Hello"));
    // event(new MpesaTransactionComplete('Succesadfghjkljhgfdsadfghjk asdfghjmkmhgfdsadf gbvasdfghjss', '9cee3126-3546-4d50-ae78-cfb653a15195', 'success'));

    // Log::info(Carbon::now() -> format('YmdHis'));

    // $command = escapeshellcmd("resources/js/node_scripts/linkWhatsApp.sh");
    // $output = shell_exec($command);

    // return response()->json(['output' => $output, 'message' => 'WhatsApp linking process started.']);
});

Route::get('/wp', function() {
    // $client = new Client();
    // $response = $client->post('https://api.gupshup.io/sm/api/v1/msg', [
    //     'headers' => [
    //         'apikey' => 'whqhdgz9iucackfblxd0vkhdyaxerrwx',
    //         'Content-Type' => 'application/x-www-form-urlencoded'
    //     ],
    //     'form_params' => [
    //         'channel' => 'whatsapp',
    //         'source' => '+254797727253',
    //         'destination' => '+254705715099',
    //         'message' => 'Hello, this is a test message!'
    //     ]
    // ]);


    // echo $response->getBody();
     // Create a new Guzzle HTTP client
     $client = new Client();

     // Define the URL
     $url = 'https://api.ultramsg.com/instance93084/messages/chat';
 
     // Define the parameters to be sent in the POST request
     $params = [
         'token' => 'krqt75rr7gzh0bhl',
         'to'    => '120363328427282938@g.us',
         'body'  => 'Blah blah fish cake'
     ];
 
     // Send the POST request
     $response = $client->post($url, [
         'headers' => [
             'Content-Type' => 'application/x-www-form-urlencoded',
         ],
         'form_params' => $params,
     ]);
 
     // Return or process the response as needed
     return $response->getBody()->getContents();
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
    // Log::info($request -> all());
});

Route::post('/webhooked', function (Request $request) {
    // dd(Telegram::commandsHandler(true));
    // dd(Telegram::getWebhookUpdate());
    
    // dd(Telegram::addCommand(App\TelegramCommands\startCommand::class));
    return response() -> json(['error' => 'forbidden'], 403);
});
Route::get('{any}', function () {
    // dd(Telegram::commandsHandler(true));
    // dd(Telegram::getWebhookUpdate());
    
    // dd(Telegram::addCommand(App\TelegramCommands\startCommand::class));
    return response() -> json(['error' => 'forbidden'], 403);
});

