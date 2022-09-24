<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MpesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Mpesa::factory(10)->create();
    }
}
