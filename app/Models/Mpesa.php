<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Mpesa extends Model
{
    use HasFactory,TraitUuid;
    
    protected $fillable = [
        'id',
        'first_name',
        'middle_name',
        'last_name',
        'msisdn',
        'bill_ref_number',
        'mpesa_transaction_id',
        'transation_time',
        'status',
        'amount'
    ];

    public function User(){
        return $this -> belongsTo(User::class);
    }
}
