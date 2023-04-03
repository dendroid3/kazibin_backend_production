<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;


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
        'taker',
        'code',
        'expiry_time',
        'pay_day',
    ];

    public function Files(){
        return $this -> hasMany(Taskfile::class);
    }

    public function Offers(){
        return $this -> hasMany(Taskoffer::class);
    }

    public function Invoice(){
        return $this -> belongsTo(Invoice::class);
    }

    public function user(){
        return $this -> belongsTo(User::class);
    }

    public function broker(){
        return $this -> belongsTo(Broker::class);
    }

    public function writer(){
        return $this -> belongsTo(Writer::class);
    }

    public function bids(){
        return $this -> hasMany(Bid::class);
    }
 
    public function ratings(){
        return $this-> hasMany(Rating::class);
    }

    public function Timestamps(){
        return $this -> hasOne(Timestamp::class);
    }

    public function messages(){
        return $this -> hasMany(Taskmessage::class);
    }

}
