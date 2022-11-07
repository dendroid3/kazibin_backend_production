<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   $page_cost = rand(150,450);
        $pages = rand(1,20);
        return [
            'topic' => $this->faker->name(),
            'unit' => 'Physics',
            'type' => 'Article',
            'instructions' => $this->faker->paragraph(10),
            'broker_id' => "e6ca266c-aa02-40b4-a9a0-f3ceaf94b1f6", # rand(1,20),
            'pages' =>  $pages,
            'page_cost' => $page_cost,
            'expiry_time' => Carbon::now()->addMinutes(rand(360, 7200))->toDateTimeString(),
            'full_pay' => $pages * $page_cost,
            'pay_day' => $this -> fakePayDay(),
            'status' => 1,
            'code' => $this->fakeCode()
        ];
    }

    public function fakeUnit(){
        $units = array('Calculus', 'Data Analysis', 'Political Science', 'Steel Structures', 'Applied Mathematics', 'Microbiology', 'Law', 'Physics', 'Analytical chemistry', 'Python');
        $random_number = floor(rand(0, (count($units) - 1 )));
        return $units[$random_number];
    }

    public function fakeType(){
        $types = array('Essay', 'Report', 'Trascription', 'Article', 'Programming');
        $random_number = floor(rand(0, (count($types) - 1 )));
        return $types[$random_number];
    }

    public function fakeExpiryTime(){

    }

    public function fakePayDay(){
        $base = floor(rand(1,4));
        switch ($base) {
            case '1':
                //on approval
                return '1997-9-17 00:00:00';
                break;
            case '2':
                //on delivery
                return '1965-5-28 00:00:00';
                break;
            
            default:
                //today+>5days - 14 days
                return Carbon::now()->addMinutes(rand(7200, 20160))->toDateTimeString();
                break;
        }
    }

    public function fakeCode(){
        return strtoupper(Str::random(rand(3,5))) . '-' . strtoupper(Str::random(rand(3,6))) . rand(45,123);
    }
}
