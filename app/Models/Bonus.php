<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'amount',
        'description',
    ];

    public function Invoice(){
        return $this -> belongsTo(Invoice::class);
    }
}
