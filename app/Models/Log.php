<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;


class Log extends Model
{
    use HasFactory, TraitUuid;
    protected $fillable = [
            'user_id',
            'foreign_id',
            'code',
            'message'
    ];
}
