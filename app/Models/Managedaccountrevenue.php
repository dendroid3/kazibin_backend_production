<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Managedaccountrevenue extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'status',
        'managedaccount_id',
        'type',
        'description',
        'amount',
    ];

    public function managedAccount() {
        return $this -> belongsTo(Managedaccount::class);
    }
}
