<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class generateNetwork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:generate';

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
        $broker_id = "88793c67-5178-4737-95f2-7861f6863b81";
        $writer_id = "8c22f2c5-db03-4092-a8db-7879229d2ce1";
        $users = User::where('id', '!=', "971f523f-70b2-48e7-aee8-e8146a58c484") -> take(50) -> get();

        for ($i=0; $i < 50; $i++) { 
            $base = floor(rand(1,10));
            if($base > 5){
                $user = $users[$i] -> broker -> id;
                $network = DB::table('broker_writer') -> insert([
                    'writer_id' => $writer_id,
                    'broker_id' => $user,
                    'cost_per_page' => 300
                ]);
                Log::info('broker');
                Log::info($base);
                Log::info($network);
            } else {

                $user = $users[$i] -> writer -> id;
                $network = DB::table('broker_writer') -> insert([
                    'writer_id' => $user,
                    'broker_id' => $broker_id,
                    'cost_per_page' => 300
                ]);
                Log::info('writer');
                Log::info($base);
                Log::info($network);
            }
        }
        return 0;
    }
}
