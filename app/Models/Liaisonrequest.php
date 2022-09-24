<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use \App\Traits\TraitUuid;


use App\Models\Requestmessage;

class Liaisonrequest extends Model
{
    use HasFactory, TraitUuid;
    protected $fillable = [
        'broker_id',
        'writer_id',
        'initiator_id',
        'cost_per_page',
        'pay_day',
        'status'
    ];

    
    public static function boot(){
        parent::boot();

        static::created(function ($request){

            $message = new Requestmessage;
            $message -> liaisonrequest_id = $request -> id;
            $message -> writer_id = $request -> writer_id;
            $message -> broker_id = $request -> broker_id;
            $message -> message = "--- New Request by " . Auth::user() -> username . " ---";
            $message -> user_id = 1;
            $message -> save();

        });
     
    }
       
    public function writer(){
        return $this -> hasOne(Writer::class);
    }

    public function broker(){
        return $this -> hasOne(Broker::class);
    }

    public function messages(){
        return $this -> hasMany(Requestmessage::class);
    }
}
