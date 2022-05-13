<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Usercode extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'broker_score',
        'writer_score',
        'net_score'
    ];

    public function user(){
        return $this -> belongsTo(User::class);
    }
}
