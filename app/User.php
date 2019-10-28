<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
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

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    //Xem lại chat

    //Xem lại đánh giá

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
}
