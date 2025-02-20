<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class seedManagedAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:managedaccounts';

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
        $taskers = \App\Models\User::query() -> where('role', 'tasker') -> get();

        foreach ($taskers as $tasker) {
            // create 5 - 10 managed_accounts for each tasker
            $users = \App\Models\User::query()->where('role', 'user')->get();
            $userIds = $users->pluck('id')->toArray();

            for ($i = 0; $i < rand(5, 10); $i++) {
                $managedAccount = \App\Models\Managedaccount::create([
                    'user_id' => $userIds[array_rand($userIds)],
                    'code' => strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(3)),
                    'tasker_id' => $tasker->tasker->id,
                    'email' => 'user' . rand(1, 1000) . '@example.com',
                    'provider' => 'provider' . rand(1, 1000),
                    'payday' => rand(1, 30),
                    'provider_identifier' => 'provider_identifier' . rand(1, 1000),
                    'tasker_rate' => $taskerRate = rand(10, 80),
                    'owner_rate' => $ownerRate = rand(10, 100 - $taskerRate),
                    'jobraq_rate' => 100 - $taskerRate - $ownerRate,
                    'status' => ['pending', 'active', 'closed'][array_rand(['pending', 'active', 'closed'])],
                ]);

                $this->line('<fg=green;> Account' . $i . ': ' . $managedAccount -> provider . ' Created </>');

                if($managedAccount -> status == 'pending'){
                    $this->line('<fg=red;> No Revenue added since the account is pending. </>');
                } else {
                    for ($j = 0; $j < rand(4, 10); $j++) {
                        $revenue = \App\Models\Managedaccountrevenue::create([
                            'managedaccount_id' => $managedAccount->id,
                            'amount' => rand(100, 1000),
                            'type' => 'Debit',
                            'description' => 'Revenue ' . $j,
                        ]);

                        $this->line('<fg=blue;> Revenue' . $j . ': ' . $revenue -> amount . '(' . $revenue -> type . ') Created </>');
                    }
                }
            }
        }
        return 0;
    }
}
