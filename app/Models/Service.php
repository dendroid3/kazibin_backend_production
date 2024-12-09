<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Service extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'category',
        'name',
        'cost'
    ];
}
