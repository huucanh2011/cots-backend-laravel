<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DateDepartureTour extends Model
{
    protected $guarded = [];

    public function tour()
    {
        return $this->belongsTo('App\Tour', 'tour_id', 'id');
    }
}
