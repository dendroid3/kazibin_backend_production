<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Traits\TraitUuid;

class Managedaccountdetail extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'managedaccount_id',
        'title',
        'description'
    ];

    public function Managedaccount() {
        return $this -> belongsTo(Managedaccount::class);
    }

}
