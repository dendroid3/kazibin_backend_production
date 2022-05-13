<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Taskfile extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'task_id',
        'url',
        'name'
    ];

    public function Task(){
        return $this -> belongsTo(Task::class);
    }
}

