<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Managedaccount extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'status',
        'user_id',
        'email',
        'provider',
        'provider_identifier'
    ];

    public function User() {
        return $this -> belongsTo(User::class);
    }

    public function Details() {
        return $this -> hasMany(Managedaccountdetail::class);
    }

    public function Revenue() {
        return $this -> hasMany(Managedaccountrevenue::class);
    }
}
