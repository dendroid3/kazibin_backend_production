<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'mpesa_transaction_id',
        'id',
        'bid_id',
        'task_id',
        'service_id',
        'description',
        'type',
        'amount'
    ];

    public function User(): BelongsTo
    {
        return $this -> belongsTo(User::class);
    }


    public function Revenue(): HasOne
    {
        return $this -> hasOne(Revenue::class);
    }

}
