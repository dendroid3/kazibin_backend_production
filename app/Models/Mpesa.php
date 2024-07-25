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
        'checkout_request_id',
        'user_id',
        'status',
        'amount',
        'paying_phone_number',
        'receipt_number',
        'transaction_date'
    ];

    public function User(){
        return $this -> belongsTo(User::class);
    }
}
