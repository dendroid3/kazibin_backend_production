<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;
use Illuminate\Support\Facades\Auth;

class Taskoffer extends Model
{
    use HasFactory, TraitUuid;
    protected $fillable = [
        'writer_id',
        'task_id',
        'status'
    ];

    public static function boot(){
        parent::boot();

        static::created(function ($request){
            
            $message = new Taskoffermessage();
            $message -> user_id = 1;
            $message -> id = Str::orderedUuid() -> toString();
            $message -> taskoffer_id = $request -> id;
            $message -> message = "--- New Offer by " . Auth::user() -> username . " ---";
            $message -> save();
        });
    }
    
    public function task(){
        return $this -> belongsTo(Task::class);
    }

    public function messages(){
        return $this -> hasMany(Taskoffermessage::class);
    }

    public function writer(){
        return $this -> belongsTo(Writer::class);
    }

    
}
