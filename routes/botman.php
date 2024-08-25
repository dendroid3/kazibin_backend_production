<?php

use App\Conversations\StartConversation;
use Illuminate\Support\Facades\Log;

$botman = resolve('botman');

$botman->hears('/start', function($bot) {
    Log::info("start called");
    $bot -> reply("Hello");
});

$botman->hears('/stop', function($bot) {
    Log::info("stop called");
    $bot -> reply("Hello");
});
