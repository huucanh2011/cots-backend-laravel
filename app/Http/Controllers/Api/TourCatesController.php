<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\TourCategory;

class TourCatesController extends ApiController
{
    public function index()
    {
        $tourCates = TourCategory::all();

        return $this->respond($tourCates);
    }
}
