<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Taskoffermessage extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'taskoffer_id',
        'message',
        'type',
        'delivered_at',
        'read_at'
    ];

    public static function boot(){
        parent::boot();

        static::created(function ($request){
            $task_offer = Taskoffer::find($request -> taskoffer_id);
            $task_offer -> updated_at = Carbon::now();
            $task_offer -> push();

            $task = $task_offer -> task;
            $task -> updated_at = Carbon::now();
            $task -> push();
        });
    }

    public function taskOffer(){
        return $this -> belongsTo(Taskoffer::class);
    }
}
