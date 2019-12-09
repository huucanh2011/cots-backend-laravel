<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TourOrder extends Model
{
    protected $guarded = [];

    public function user(){
    	return $this->belongsTo('App\User','user_id','id');
    }

    public function dateDepartureTour(){
    	return $this->belongsTo('App\DateDepartureTour','date_departure_tour_id','id');
    }
}
