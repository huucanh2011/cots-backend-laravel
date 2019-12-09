<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordRequest;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo('App\Role', 'role_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany('App\Like', 'user_id', 'id');
    }
    
    public function posts()
    {
        return $this->hasMany('App\Post', 'user_id', 'id');
    }

    public function tourOrders()
    {
        return $this->hasMany('App\TourOrder', 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'user_id', 'id');
    }

    public function tours()
    {
        return $this->hasMany('App\Tour', 'user_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordRequest($token));
    }
}
