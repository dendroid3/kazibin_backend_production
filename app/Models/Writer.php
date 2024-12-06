<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use \App\Traits\TraitUuid;


class Writer extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'id'
    ];
    
    public function user(){
        return $this -> belongsTo(User::class);
    }
    
    public function liaisonRequests(){
        return $this -> hasMany(Liaisonrequest::class);
    }
    
    public function brokers(){
        return $this -> belongsToMany(Broker::class, 'broker_writer',  'writer_id', 'broker_id');
    }

    public function offers(){
        return $this -> hasMany(Taskoffer::class);
    }

    public function bids(){
        return $this -> hasMany(Bid::class);
    }

    public function tasks(){
        return $this -> hasMany(Task::class);
    }

    public function Invoices(){
        return $this -> hasMany(Invoice::class);
    }
    
    public function ratings(){
        return $this-> hasMany(Rating::class) -> where('initiator_id', '!=', Auth::user() -> id);
    }


}
