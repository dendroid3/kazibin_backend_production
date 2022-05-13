<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liaisonrequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'broker_id',
        'writer_id',
        'initiator_id'
    ];
}
