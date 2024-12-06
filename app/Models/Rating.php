<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'writers-id',
        'broker_id',
        'initiator_id',
        'rating',
        'review'
    ];

    public function task(){
        return $this -> belongsTo(Task::class);
    }

    public function writer(){
        return $this -> belongsTo(Writer::class);
    }

    public function broker(){
        return $this -> belongsTo(Broker::class);
    }

}
