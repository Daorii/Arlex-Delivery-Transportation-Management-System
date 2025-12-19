<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Sipadetail;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\TripDetail;
use Illuminate\Http\Request;

class DriverTripController extends Controller
{
    public function show(Request $request, $dispatch_id)
{
    $driverId = session('driver_id');
    
    if (!$driverId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }

    $dispatch = Dispatch::where('dispatch_id', $dispatch_id)
        ->where('driver_id', $driverId)
        ->first();

    if (!$dispatch) {
        return redirect()->route('driver.dispatches')->with('error', 'Dispatch not found');
    }

    $driver = Driver::where('driver_id', $dispatch->driver_id)->first();
    $truck = Truck::where('truck_id', $dispatch->truck_id)->first();
    $sipa = $dispatch->sipa;

    $sipaDetails = Sipadetail::where('sipa_id', $dispatch->sipa_id)->get();
    $sipaDetail = Sipadetail::where('sipa_id', $dispatch->sipa_id)->first();
    
    // Search and pagination
    $search = $request->get('search');
    $perPage = 10;
    
    $query = TripDetail::where('dispatch_id', $dispatch_id);
    
    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('container_no', 'like', "%{$search}%")
              ->orWhere('eir_no', 'like', "%{$search}%")
              ->orWhere('delivery_date', 'like', "%{$search}%");
        });
    }
    
    $tripDetails = $query->orderBy('detail_id', 'desc')->paginate($perPage);

    // Check if the SIPA detail has expired
    $isExpired = false;
    if ($sipaDetail && $sipaDetail->effectivity_to) {
        $isExpired = now()->gt($sipaDetail->effectivity_to);
    }

    return view('driver.trips_details', compact('dispatch', 'driver', 'truck', 'sipa', 'sipaDetails', 'sipaDetail', 'tripDetails', 'isExpired', 'search'));
}

    public function store(Request $request)
{
    $driverId = session('driver_id');
    
    if (!$driverId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }

    $validated = $request->validate([
        'dispatch_id' => 'required|integer',
        'sipa_detail_id' => 'required|integer',
        'container_no' => 'required|string',
        'eir_no' => 'required|string',
        'delivery_date' => 'required|date',
    ]);

    // Verify that the dispatch belongs to this driver
    $dispatch = Dispatch::where('dispatch_id', $validated['dispatch_id'])
        ->where('driver_id', $driverId)
        ->first();

    if (!$dispatch) {
        return redirect()->route('driver.dispatches')->with('error', 'Unauthorized access');
    }

    // NEW: Check if the SIPA detail has expired
    $sipaDetail = Sipadetail::where('sipa_detail_id', $validated['sipa_detail_id'])->first();
    
    if ($sipaDetail && $sipaDetail->effectivity_to && now()->gt($sipaDetail->effectivity_to)) {
        return redirect()->route('driver.trip.details', ['dispatch_id' => $validated['dispatch_id']])
            ->with('error', 'Cannot add trip detail. This dispatch expired on ' . date('M d, Y', strtotime($sipaDetail->effectivity_to)));
    }

    TripDetail::create([
        'dispatch_id' => $validated['dispatch_id'],
        'sipa_detail_id' => $validated['sipa_detail_id'],
        'container_no' => $validated['container_no'],
        'eir_no' => $validated['eir_no'],
        'delivery_date' => $validated['delivery_date'],
        'is_verified' => 0,
        'verified_by' => null,
        'verified_at' => null
    ]);

    return redirect()->route('driver.trip.details', ['dispatch_id' => $validated['dispatch_id']])
        ->with('success', 'Trip detail added successfully');
}

}