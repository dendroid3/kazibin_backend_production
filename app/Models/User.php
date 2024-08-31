<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

use Laravel\Passport\HasApiTokens;
use \App\Traits\TraitUuid;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TraitUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'phone_number',
        'email',
        'email_verification',
        'phone_verification',
        'credential_verification',
        'password',
        'level',
        'interests',
        'bio',
        'availabile',
        'cost_per_page',
        'pay_day',
        'broker_score',
        'writer_score',
        'code',
        'last_activity'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot(){
        parent::boot();

        static::created(function ($request){
            /* 
                A user does not specify to be a writer or a broker, the system shall categorise them according to their activities on the platform. As such, each user will have both a 
                writer and broker's profile
            */
            $broker = new Broker; 
            $broker -> id = $request -> id;
            $broker -> user_id = $request -> id;
            $broker -> save();

            $writer = new Writer;
            $writer -> id = $request -> id;
            $writer -> user_id = $request -> id;
            $writer -> save();

            /* 
                User codes are a summary of the activities of a user on the platform. His 'broker_score' increases once he does an activity associated with brokers such as posting a task and paying for it
                His 'writer_score' increases once they do activities associated with being a writer, such as accepting an offer or winning a bid. The 'net_score' which is "broker_score" - "writer_score"
                shall be used to determine whether a user is a broker or writer.
            */

        });
    }

    public function OauthAccessToken(){
        return $this -> hasOne(OauthAccessToken::class);
    }

    public function writer(){
        return $this -> hasOne(Writer::class);
    }

    public function broker(){
        return $this -> hasOne(Broker::class);
    }

    public function tasks(){
        return $this -> hasMany(Task::class);
    }

    public function transactions(){
        return $this -> hasMany(Transaction::class);
    }

    public function mpesas(){
        return $this -> hasMany(Mpesa::class);
    }

    public function verifications(){
        return $this -> hasMany(Verification::class);
    }

    public function account(){
        return $this -> hasMany(Account::class);
    }

}
