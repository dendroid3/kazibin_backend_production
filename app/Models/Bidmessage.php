<?php

namespace App\Models;

use App\Traits\TraitUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Bidmessage extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'id',
        'user_id',
        'bid_id',
        'message',
        'type',
        'delivered_at',
        'read_at'
    ];

    public static function boot(){
        parent::boot();

        static::created(function ($request){
            $bid = Bid::find($request -> bid_id);
            $bid -> updated_at = Carbon::now();
            $bid -> push();

            $task = $bid -> task;
            $task -> updated_at = Carbon::now();
            $task -> push();
        });
    }

    public function bid(){
        return $this -> belongsTo(Bid::class);
    }
}
