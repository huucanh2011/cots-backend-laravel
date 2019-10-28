<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageLocation extends Model
{
    protected $guarded = [];

    public function location()
    {
        return $this->belongsTo('App\Location', 'location_id', 'id');
    }
}
