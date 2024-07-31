<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Task extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'id',
        'broker_id',
        'writer_id',
        'invoice_id',
        'status',
        'topic',
        'unit',
        'pages',
        'page_cost',
        'full_pay',
        'difficulty',
        'instructions',
        'type',
        'takers',
        'code',
        'expiry_time',
        'pay_day',
        'verified_only'
    ];

    public function Files(): HasMany
    {
        return $this -> hasMany(Taskfile::class);
    }

    public function Offers(): HasMany
    {
        return $this -> hasMany(Taskoffer::class);
    }

    public function Invoice(): BelongsTo
    {
        return $this -> belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class);
    }

    public function broker(): BelongsTo
    {
        return $this -> belongsTo(Broker::class);
    }

    public function writer(): BelongsTo
    {
        return $this -> belongsTo(Writer::class);
    }

    public function bids(): HasMany
    {
        return $this -> hasMany(Bid::class);
    }
 
    public function ratings(): HasMany
    {
        return $this-> hasMany(Rating::class);
    }

    public function Timestamps(): HasOne
    {
        return $this -> hasOne(Timestamp::class);
    }

    public function messages(): HasMany
    {
        return $this -> hasMany(Taskmessage::class);
    }

}
