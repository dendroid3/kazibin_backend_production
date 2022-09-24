<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MpesaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            
            
            'id' => Str::orderedUuid() -> toString(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->lastName(),
            'last_name' => $this->faker->lastName(),
            'msisdn' => floor(rand(111111111,999999999)),
            'bill_ref_number' =>  strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(3)),
            'mpesa_transaction_id' =>  strtoupper(Str::random(3)) . floor(rand(10,99)) . strtoupper(Str::random(2)) . floor(rand(100,999)),
            'transation_time' =>  Carbon::now()->subMinutes(floor(rand(3,300))),
            'amount' => floor(rand(100,1000))
        ];
    }
}
