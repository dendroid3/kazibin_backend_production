<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Taskoffer;
use Illuminate\Support\Str;

class GenerateOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:generate';

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
        $tasks = Task::where('status', 1) -> take(100) -> get();
        foreach ($tasks as $task) {
            $offer = new Taskoffer();
            $offer -> status = floor(rand(1,4));
            $offer -> id = Str::orderedUuid() -> toString();
            $offer -> task_id = $task -> id;
            $offer -> writer_id = "8c22f2c5-db03-4092-a8db-7879229d2ce1";
            $offer -> save();
         } 
       
        return 'offers generated';
    }
}
