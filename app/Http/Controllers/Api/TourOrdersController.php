<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\TourOrder;

class TourOrdersController extends ApiController
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function index()
    {
        $tourOrders = TourOrder::with('dateDepartureTour', 'user')->latest()->paginate(10);

        return $this->respond($tourOrders);
    }

    public function store(Request $request)
    {
        // $customerName = "";
        // $customerEmail = "";
        // $customerPhoneNumber = "";
        // $customerAddress = "";
        // $userId = "";
        // if(auth()->check()) {
        $customerName = auth()->user()->name;
        $customerEmail = auth()->user()->email;
        $customerPhoneNumber = auth()->user()->phone_number;
        $customerAddress = auth()->user()->address;
        $userId = auth()->user()->id;
        // } else {
        //     $customerName = $request->name;
        //     $customerEmail = $request->email;
        //     $customerPhoneNumber = $request->phone_number;
        //     $customerAddress = $request->address;
        // }
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

    public function show($id)
    {
        return $this->respond(TourOrder::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        //
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
        $tourOrder = TourOrder::with('dateDepartureTour', 'user')->findOrFail($request->id);
        $newDate = date("Y-m-d H:i:s");

        try {
            $tourOrder->update([
                'is_active' => $request->is_active,
                'date_active' => $newDate,
            ]);
            return $this->respond($tourOrder);

        } catch (Exception $e) {
            return response()->json([ 'message' => $e->getMessage() ]);
        }
    }
}
