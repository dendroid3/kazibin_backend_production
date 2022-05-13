<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;


class Task extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'broker_id',
        'writer_id',
        'topic',
        'unit',
        'pages',
        'page_cost',
        'instructions',
        'expiry_time',
        'status',
        'pay_day',
        'type',
        'takers',
        'code',
        'difficulty'
    ];

    public function Files(){
        return $this -> hasMany(Taskfile::class);
     }
}
