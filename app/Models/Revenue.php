<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'transaction_id',
        'type',
        'amount'
    ];

    public function transaction() {
        return $this -> belongsTo(Transaction::class);
    }

    
}
