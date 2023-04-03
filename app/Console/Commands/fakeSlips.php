<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class fakeSlips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fakeSlips';

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
        $monthAndDays = [
            ['name' => 'Jan', 'days' =>  31],
            ['name' => 'Feb', 'days' =>  28],
            ['name' => 'Mar', 'days' =>  31],
            ['name' => 'Apr', 'days' =>  30],
            ['name' => 'May', 'days' =>  31],
            ['name' => 'Jun', 'days' =>  30],
            ['name' => 'Jul', 'days' =>  31],
            ['name' => 'Aug', 'days' =>  31],
            ['name' => 'Sep', 'days' =>  30],
            ['name' => 'Oct', 'days' =>  31],
            ['name' => 'Nov', 'days' =>  30],
            ['name' => 'Dec', 'days' =>  31],
        ];

        $slips = [];
        for ($i=0; $i < 12; $i++) { 
            for ($j=1; $j <= $monthAndDays[$i]['days']; $j++) { 
                $score = Floor(rand(0,100));
                $flag = Floor(rand(0,10)) > 5 ? 'Win' : 'Loss';
                $this->line('<fg=green;>' . $monthAndDays[$i]['name'] . ' => ' . $j . ' ' . $flag . ' => ' . $score . '</>');

                $slip = [
                    'month' => $monthAndDays[$i]['name'],
                    'day' => $j,
                    'flag' => $flag,
                    'score' => $score
                ];

                array_push($slips, $slip);
            }
            // dd($monthAndDays[0]);
        }
        return 0;
    }
}
