<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $guarded = [];

    public function user(){
    	return $this->belongsTo('App\User','user_id','id');
    }

    public function post(){
    	return $this->belongsTo('App\Post','post_id','id');
    }
}
