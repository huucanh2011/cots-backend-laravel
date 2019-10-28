<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImagePost extends Model
{
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo('App\Post', 'post_id', 'id');
    }
}
