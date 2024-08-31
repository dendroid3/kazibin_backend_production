<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class sendWhatsAppMessage extends Command
{
    protected $signature = 'whatsapp:send {message}';
    protected $description = 'Send a message to a WhatsApp group using Puppeteer';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $groupName = env('WHATSAPP_NOTIFICATIONS_GROUP');
        $message = $this->argument('message');

        // Run the Puppeteer script with the group name and message as arguments
        $command = "node resources/js/node_scripts/sendMessage.js \"$groupName\" \"$message\"";
        Log::info($command);
        shell_exec($command);

        // $command = escapeshellcmd("resources/js/node_scripts/startXFB.sh '$groupName' '$message'");
        // $output = shell_exec($command);

        $this->info('Message sent!');

        return 0;
    }
}
