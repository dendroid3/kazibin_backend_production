<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Invoice extends Model
{
    use HasFactory, TraitUuid;
    
    protected $fillable = [
        'broker_id',
        'writer_id',
        'status',
        'jobs_signature',
        'amount',
        'code',
        'tasks_signature'
    ];

    public function Tasks(){
        return $this -> hasMany(Task::class);
    }

    public function Broker(){
        return $this -> belongsTo(Broker::class);
    }
    
    public function Writer(){
        return $this -> belongsTo(Writer::class);
    }

    public function Bonuses(){
        return $this -> hasMany(Bonus::class);
    }
    public function Fines(){
        return $this -> hasMany(Fine::class);
    }
}
