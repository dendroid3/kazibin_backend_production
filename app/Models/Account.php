<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TraitUuid;

class Account extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'code',
        'title',
        'rating',
        'profile_origin',
        'profile_gender',
        'total_orders',
        'pending_orders',
        'cost',
        'negotiable',
        'display',
        'expiry'
    ];

    public function User() {
        return $this -> belongsTo(User::class) -> select('username', 'code', 'email', 'phone_number');
    }

    public function Files() {
        return $this -> hasMany(Accountfile::class) -> select('url', 'name');
    }


}
