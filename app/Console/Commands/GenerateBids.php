<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bid;
use App\Models\Task;
use Illuminate\Support\Str;

class GenerateBids extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bids:generate';

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
        
        for ($i=0; $i < 50; $i++) { 
            $bid = new Bid;
            $bid -> id = Str::orderedUuid() -> toString();
            $bid -> task_id = $tasks[floor(rand(0, 99))] -> id;
            $bid -> status = floor(rand(1,4));
            $bid -> writer_id = "8c22f2c5-db03-4092-a8db-7879229d2ce1";
            $bid -> save();
        }
        return 'bids generated';
    }
}
