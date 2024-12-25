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
        'code',
        'status',
        'payday',
        'email',
        'provider',
        'provider_identifier',
        'tasker_id',
        'tasker_rate',
        'owner_rate',
        'jobraq_rate',
        'proxy'
    ];

    public function user() {
        return $this -> belongsTo(User::class);
    }

    public function details() {
        return $this -> hasMany(Managedaccountdetail::class);
    }

    public function tasker() {
        return $this -> belongsTo(Tasker::class);
    }

    public function revenue() {
        return $this -> hasMany(Managedaccountrevenue::class);
    }
}
