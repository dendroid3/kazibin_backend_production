<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserFactory extends Factory
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
            'username' => substr($this->faker->name(), 0, 25),
            'phone_number' => rand(111111111,999999999),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'broker_score' => floor(rand(4,2000)),
            'writer_score' => floor(rand(4,2000)),
            'code' => strtoupper(Str::random(2)) . '-' . strtoupper(Str::random(3)),
            'last_activity' => Carbon::now()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
