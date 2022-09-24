<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Task;
use Illuminate\Support\Str;

class createInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $task_count = Task::where('status', 3) -> where('writer_id', "8c22f2c5-db03-4092-a8db-7879229d2ce1") -> count();

        for ($i=0; $i < $task_count; $i++) { 
            $task = Task::where('status', 3) -> where('writer_id', "8c22f2c5-db03-4092-a8db-7879229d2ce1") -> first();
            $invoice = new Invoice;
            $invoice -> writer_id = "8c22f2c5-db03-4092-a8db-7879229d2ce1";
            $invoice -> broker_id = "e2bfb9a1-2d03-41b7-9977-905bcd667d26";
            $invoice -> status = 1;
            $invoice -> code = strtoupper(Str::random(2)) . '-' . strtoupper(Str::random(3));
            $invoice -> tasks_signature = $task -> id . "_";
            $invoice -> amount = $task -> full_pay;
            $invoice -> save();

            $task -> status = 8;
            $task -> push();
        }
        return 'invoices created';
    }
}
