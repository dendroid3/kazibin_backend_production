<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class GenerateTakenTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taken:generate';

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
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $task -> status = floor(rand(2,8));
            // $task -> writer_id = '8c22f2c5-db03-4092-a8db-7879229d2ce1';
            $task -> difficulty = floor(rand(3,9));
            $task -> push();
        } 
        return 'generated taken tasks';
    }
}
