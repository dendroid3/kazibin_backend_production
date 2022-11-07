<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasktimestamp extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'assigned_at',
        'completed_at',
        'cancelled_at',
        'invoiced_at',
        'pay_initialised_at',
        'pay_confirmed_at',
        'cancelation_initiated_at',
    ];

    public function Task(){
        return $this -> belongsTo(Task::class);
    }

}
