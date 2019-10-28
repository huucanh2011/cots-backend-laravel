<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TourCategory extends Model
{
    protected $guarded = [];

    public function tours()
    {
        return $this->hasMany('App\Tour', 'tourcate_id', 'id');
    }
}
