<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;


class Broker extends Model
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

    public function writers(){
        return $this -> belongsToMany(Writer::class, 'broker_writer', 'broker_id', 'writer_id');
    }

    public function tasks(){
        return $this -> hasMany(Task::class);
    }

    public function Invoices(){
        return $this -> hasMany(Invoice::class);
    }

    public function ratings(){
        return $this-> hasMany(Rating::class);
    }

}
