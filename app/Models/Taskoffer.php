<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Taskoffer extends Model
{
    use HasFactory, TraitUuid;
    protected $fillable = [
        'writer_id',
        'task_id'
    ];
}
