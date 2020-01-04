<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\User;
use App\Post;
use App\Tour;
use App\TourOrder;
use DB;

class DashboardController extends ApiController
{
    public function __construct()
    {
        $this->middleware(['admin', 'jwt.auth']);
    }

    public function index()
    {
        $nowDate = date("Y-m-d");
        $nowYear = date("Y");
        $nowMonth = date("m");
        $countMember = User::where('is_active', 1)->count();
        $countPost = Post::where('is_active', 1)->count();
        $countTour = Tour::where('is_active', 1)->count();

        $tourOrders = DB::table('tour_orders')
            ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
            ->whereDate('tour_orders.created_at', $nowDate)
            ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $tourOrdersMonthly = DB::table('tour_orders')
            ->where('is_active', 1)
            ->whereYear('created_at', $nowYear)
            ->select(DB::raw('MONTH(created_at) as month, COUNT(*) as total_tour'))
            ->groupBy('month')
            ->get();

        $postsMonthly = DB::table('posts')
            ->where('is_active', 1)
            ->whereMonth('created_at', $nowMonth)
            ->select(DB::raw('DATE(created_at) as date, COUNT(*) as total_post'))
            ->groupBy('date')
            ->get();

        $years = DB::table('tour_orders')
            ->where('is_active', 1)
            ->select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->get();

        return response()->json([
            'count' => [
                'countMember' => $countMember,
                'countPost' => $countPost,
                'countTour' => $countTour, 
            ],
            'tourOrders' => $tourOrders,
            'tourOrdersMonthly' => $tourOrdersMonthly,
            'postsMonthly' => $postsMonthly,
            'years' => $years
        ]);
    }
}
