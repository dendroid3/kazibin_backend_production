<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_id',
        'amount',
        'description',
        'invoice_id'
    ];
    public function Invoice(){
        return $this -> belongsTo(Invoice::class);
    }
}
