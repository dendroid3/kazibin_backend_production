<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Requestmessage extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'user_id',
        'writer_id',
        'broker_id',
        'message',
        'liaisonrequest_id',
        'fetched_at',
        'read_at',
        'type'
    ];

    public function liaisonRequest(){
        return $this -> belongsTo(Liaisonrequest::class);
    }
}
