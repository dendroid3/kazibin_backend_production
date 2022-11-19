<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bid extends Model
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
            
            $message = new Bidmessage();
            $message -> id = Str::orderedUuid() -> toString();
            $message -> user_id = 1;
            $message -> bid_id = $request -> id;
            $message -> message = "--- New Bid by " . Auth::user() -> username . " ---";
            $message -> save();
        });
    }

    public function writer(){
        return $this -> belongsTo(Writer::class);
    }

    public function Task(){
        return $this -> belongsTo(Task::class);
    }

    public function messages(){
        return $this -> hasMany(Bidmessage::class);
    }
}
