<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Tasker extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'status',
        'score'
    ];

    public function user() 
    {
        return $this -> belongsTo(User::class);
    }

    public function managedAccounts() 
    {
        return $this -> hasMany(Managedaccount::class);
    }
}
