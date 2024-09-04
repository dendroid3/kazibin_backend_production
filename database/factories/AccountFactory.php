<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AccountFactory extends Factory
{
   
    public function definition()
    {
        $origins = [
            'Kenyan',
            'US',
            'UK',
            'Canada'
        ];

        $titles = [
            'WritersBay',
            'UvoCorp',
            'RemoteTask',
            '4Writers',
            'Eduson',
            'Blue Corp',
            'WritersLab'
        ];

        return [
            'user_id' => $this -> getUserId(),
            'code' => $this -> fakeCode(),
            'title' => $titles[rand()&6],
            'profile_origin' => $origins[rand()&3],
            'profile_gender' => (rand()&1 == 1 ? 'Female' : 'Male'),
            'total_orders' => rand(200,2000),
            'pending_orders' => rand()&20,
            'cost' => rand(15000,200000),
            'negotiable' => rand()&1,
            'display' => rand()&1,
            'rating' => rand(75,98),
            'expiry' => Carbon::now() -> addDays(7)
        ];
    }


    public function fakeCode(){
        return strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(3)) . rand(45,123);
    } 

    public function getUserId() {
        $users = User::all();
        $user_count = count($users);
        $user = $users[Floor(rand(0, ($user_count - 1)))];
        return $user -> id;
    }
}
