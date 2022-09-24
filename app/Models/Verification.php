<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'front_id_url',
        'back_id_url',
        'passport_url',
        'user_id',
        'transaction_id',
        'status'
    ];

    public function User() {
        return $this -> belongTo(User::class);
    }
}
