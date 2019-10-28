<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $guarded = [];

    public function user(){
    	return $this->belongsTo('App\User','user_id','id');
    }

    public function tourCate(){
    	return $this->belongsTo('App\TourCategory','tourcate_id','id');
    }

    public function imageTours()
    {
        return $this->hasMany('App\ImageTour', 'tour_id', 'id');
    }

    public function tourOrders()
    {
        return $this->hasMany('App\TourOrder', 'tour_id', 'id');
    }
}
