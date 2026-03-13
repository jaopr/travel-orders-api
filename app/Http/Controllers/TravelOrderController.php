<?php

namespace App\Http\Controllers;

use App\Models\TravelOrder;
use App\Events\TravelOrderStatusChanged;
use Illuminate\Http\Request;

class TravelOrderController extends Controller {

    public function index(Request $request) {
        $query = TravelOrder::where('user_id', auth()->id());

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->destination) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->date_from) {
            $query->where('departure_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('departure_date', '<=', $request->date_to);
        }

        return response()->json($query->get());
    }

    public function store(Request $request) {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after_or_equal:departure_date',
        ]);

        $order = TravelOrder::create([
            'user_id' => auth()->id(),
            'requester_name' => $request->requester_name,
            'destination' => $request->destination,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'status' => 'requested',
        ]);

        return response()->json($order, 201);
    }

    public function show(TravelOrder $travelOrder) {
        if ($travelOrder->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($travelOrder);
    }

    public function updateStatus(Request $request, TravelOrder $travelOrder) {
        if ($travelOrder->user_id === auth()->id()) {
            return response()->json([
                'error' => 'You cannot update the status of your own order'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,cancelled',
        ]);

        if ($travelOrder->status === 'cancelled') {
            return response()->json([
                'error' => 'Cannot update a cancelled order'
            ], 422);
        }

        if ($travelOrder->status === 'approved' && $request->status === 'approved') {
            return response()->json([
                'error' => 'Order is already approved'
            ], 422);
        }

        $travelOrder->update(['status' => $request->status]);
        event(new TravelOrderStatusChanged($travelOrder));

        return response()->json($travelOrder);
    }
}