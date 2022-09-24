<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Taskmessage extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'task_id',
        'message',
        'type',
        'delivered_at',
        'read_at'
    ];

    public static function boot(){
        parent::boot();

        static::created(function ($request){
            $task = Task::find($request -> task_id);
            $task -> updated_at = Carbon::now();
            $task -> push();

        });
    }
}
