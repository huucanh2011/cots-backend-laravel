<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    public function imageLocations()
    {
        return $this->hasMany('App\ImageLocation', 'location_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany('App\Post', 'location_id', 'id');
    }
}
