<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\TourOrder;
use DB;

class TourOrdersController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
        $userId = auth()->user()->id;

        $tourOrdersActive = DB::table('tour_orders')
            ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
            ->join('tours', 'tours.id', '=', 'date_departure_tours.tour_id')
            ->join('users', 'users.id', '=', 'tours.user_id')
            ->where('users.id', '=', $userId)
            ->where('tour_orders.is_active', '=', 1)
            ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $tourOrdersBlocked = DB::table('tour_orders')
            ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
            ->join('tours', 'tours.id', '=', 'date_departure_tours.tour_id')
            ->join('users', 'users.id', '=', 'tours.user_id')
            ->where('users.id', '=', $userId)
            ->where('tour_orders.is_active', '=', 0)
            ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return response()->json([
            'tourOrdersActive' => $tourOrdersActive,
            'tourOrdersBlocked' => $tourOrdersBlocked,
        ]);
    }

    public function searchActive(Request $request)
    {
        $userId = auth()->user()->id;
        $tourOrders = DB::table('tour_orders')
            ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
            ->join('tours', 'tours.id', '=', 'date_departure_tours.tour_id')
            ->join('users', 'users.id', '=', 'tours.user_id')
            ->where('users.id', '=', $userId)
            ->where('tour_orders.is_active', '=', 1)
            ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure');

        if($search = $request->q)
        {
            $tourOrdersActive = $tourOrders
                ->where('date_departure_tours.date_departure_code', 'LIKE', '%'.$search.'%')
                ->orWhere('tour_orders.customer_name', 'LIKE', '%'.$search.'%')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        else
        {
            $tourOrdersActive = $tourOrders
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        return $this->respond($tourOrdersActive);      
    }

    public function searchBlock(Request $request)
    {
        $userId = auth()->user()->id;
        $tourOrders = DB::table('tour_orders')
            ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
            ->join('tours', 'tours.id', '=', 'date_departure_tours.tour_id')
            ->join('users', 'users.id', '=', 'tours.user_id')
            ->where('users.id', '=', $userId)
            ->where('tour_orders.is_active', '=', 0)
            ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure');

        if($search = $request->q)
        {
            $tourOrdersBlock = $tourOrders
                ->where('date_departure_tours.date_departure_code', 'LIKE', '%'.$search.'%')
                ->orWhere('tour_orders.customer_name', 'LIKE', '%'.$search.'%')
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        }
        else
        {
            $tourOrdersBlock = $tourOrders
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        }
        return $this->respond($tourOrdersBlock);        
    }

    public function filter(Request $request)
    {
        $tourOrders = DB::table('tour_orders')
                ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
                ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure');

        if(isset($request['status'])) {
            $tourOrders = $tourOrders->where('is_active', '=', $request['status']);
        }
        if(isset($request['date'])) {
            $tourOrders = $tourOrders->where('date_departure', '=', $request['date']);
        }

        $tourOrders = $tourOrders->orderBy('created_at', 'desc')->paginate(10);

        return $this->respond($tourOrders);
    }

    public function store(Request $request)
    {
        $customerName = auth()->user()->name;
        $customerEmail = auth()->user()->email;
        $customerPhoneNumber = auth()->user()->phone_number;
        $customerAddress = auth()->user()->address;
        $userId = auth()->user()->id;

        $tourOrder = TourOrder::create([
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone_number' => $customerPhoneNumber,
            'customer_address' => $customerAddress,
            'quantity_people' => $request->quantily_people,
            'total' => $request->total,
            'note' => $request->note,
            'date_departure_tour_id' => $request->date_departure_tour_id,
            'user_id' => $userId
        ]);

        return $this->respond($tourOrder);
    }

    public function destroy($id)
    {
        $tourOrder = TourOrder::findOrFail($id);
        $tourOrder->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    public function active(Request $request)
    {        
        $tourOrder = TourOrder::findOrFail($request->id);
        $newDate = date("Y-m-d H:i:s");

        try {
            $tourOrder->update([
                'is_active' => $request->is_active,
                'date_active' => $newDate,
            ]);
            $tourOrderResp = DB::table('tour_orders')
                ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
                ->select('tour_orders.*', 'date_departure_tours.date_departure_code', 'date_departure_tours.date_departure')
                ->where('tour_orders.id', '=', $request->id)
                ->get();

            return $this->respond($tourOrderResp[0]);
        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ]);
        }
    }

    public function countTourOrderBlock()
    {
        $userId = auth()->user()->id;
        $countBlock = DB::table('tour_orders')
            ->join('date_departure_tours', 'date_departure_tours.id', '=', 'tour_orders.date_departure_tour_id')
            ->join('tours', 'tours.id', '=', 'date_departure_tours.tour_id')
            ->join('users', 'users.id', '=', 'tours.user_id')
            ->where('users.id', '=', $userId)
            ->where('tour_orders.is_active', '=', 0)
            ->select('tour_orders.*')
            ->count();

        return $countBlock;
    }
}
