<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;


class Writer extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id'
    ];
    
    public function user(){
        return $this -> belongsTo(User::class);
    }
}
