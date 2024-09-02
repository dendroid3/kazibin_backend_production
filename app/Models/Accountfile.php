<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TraitUuid;

class Accountfile extends Model
{
    use HasFactory, TraitUuid;

    protected $fillable = [
        'account_id',
        'url',
        'name'
    ];

    public function Account() {
        return $this -> belongsTo(Account::class);
    }
}
