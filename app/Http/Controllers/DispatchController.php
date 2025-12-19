<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    // Store a new dispatch
    public function store(Request $request)
{
    $validated = $request->validate([
        'sipa_id' => 'required|integer',
        'driver_id' => 'required|integer',
        'truck_id' => 'required|integer',
        'status' => 'required|string'
    ]);

    $dispatch = Dispatch::create($validated);

    // Update truck status to "On Trip"
    $truck = \App\Models\Truck::find($validated['truck_id']);
    if ($truck) {
        $truck->update(['status' => 'On Trip']);
    }

    // Update driver status to "On Trip"
    $driver = \App\Models\Driver::find($validated['driver_id']);
    if ($driver) {
        $driver->update(['status' => 'On Trip']);
    }

    return response()->json([
        'message' => 'Dispatch created successfully',
        'dispatch' => $dispatch
    ], 201);
}

    // Get all dispatches for a SIPA with driver and truck names
    public function getDispatchesBySipa($sipaId)
    {
        $dispatches = Dispatch::where('sipa_id', $sipaId)
            ->with(['driver', 'truck'])
            ->get()
            ->map(function($dispatch) {
                return [
                    'dispatch_id' => $dispatch->dispatch_id,
                    'driver_id' => $dispatch->driver_id,
                    'driverName' => $dispatch->driver 
                        ? $dispatch->driver->fname . ' ' . $dispatch->driver->lname 
                        : 'N/A',
                    'truck_id' => $dispatch->truck_id,
                    'truckName' => $dispatch->truck 
                        ? $dispatch->truck->plate_no . ' - ' . $dispatch->truck->description 
                        : 'N/A',
                    'status' => $dispatch->status,
                    'sipa_id' => $dispatch->sipa_id
                ];
            });
        
        return response()->json($dispatches);
    }

    // Update a dispatch
    public function update(Request $request, $dispatchId)
{
    $validated = $request->validate([
        'driver_id' => 'required|integer',
        'truck_id' => 'required|integer',
        'status' => 'required|string'
    ]);

    $dispatch = Dispatch::findOrFail($dispatchId);
    $oldTruckId = $dispatch->truck_id;
    $oldDriverId = $dispatch->driver_id;
    
    $dispatch->update($validated);

    // Handle truck status changes
    if ($oldTruckId != $validated['truck_id']) {
        // Set old truck back to Available (if no other dispatches using it)
        $otherDispatches = Dispatch::where('truck_id', $oldTruckId)
            ->where('dispatch_id', '!=', $dispatchId)
            ->exists();
        
        if (!$otherDispatches) {
            $oldTruck = \App\Models\Truck::find($oldTruckId);
            if ($oldTruck) {
                $oldTruck->update(['status' => 'Available']);
            }
        }

        // Set new truck to On Trip
        $newTruck = \App\Models\Truck::find($validated['truck_id']);
        if ($newTruck) {
            $newTruck->update(['status' => 'On Trip']);
        }
    }

    // Handle driver status changes
    if ($oldDriverId != $validated['driver_id']) {
        // Set old driver back to Active (if no other active dispatches)
        $otherDriverDispatches = Dispatch::where('driver_id', $oldDriverId)
            ->where('dispatch_id', '!=', $dispatchId)
            ->whereIn('status', ['Pending', 'In Transit'])
            ->exists();
        
        if (!$otherDriverDispatches) {
            $oldDriver = \App\Models\Driver::find($oldDriverId);
            if ($oldDriver) {
                $oldDriver->update(['status' => 'Active']);
            }
        }

        // Set new driver to On Trip
        $newDriver = \App\Models\Driver::find($validated['driver_id']);
        if ($newDriver) {
            $newDriver->update(['status' => 'On Trip']);
        }
    }

    return response()->json([
        'message' => 'Dispatch updated successfully',
        'dispatch' => $dispatch
    ]);
}

    // Delete a dispatch
    public function destroy($dispatchId)
{
    $dispatch = Dispatch::findOrFail($dispatchId);
    $truckId = $dispatch->truck_id;
    $driverId = $dispatch->driver_id;
    
    $dispatch->delete();

    // Set truck back to "Available" if no other active dispatches
    $otherTruckDispatches = Dispatch::where('truck_id', $truckId)->exists();
    
    if (!$otherTruckDispatches) {
        $truck = \App\Models\Truck::find($truckId);
        if ($truck) {
            $truck->update(['status' => 'Available']);
        }
    }

    // Set driver back to "Active" if no other active dispatches
    $otherDriverDispatches = Dispatch::where('driver_id', $driverId)
        ->whereIn('status', ['Pending', 'In Transit'])
        ->exists();
    
    if (!$otherDriverDispatches) {
        $driver = \App\Models\Driver::find($driverId);
        if ($driver) {
            $driver->update(['status' => 'Active']);
        }
    }

    return response()->json([
        'message' => 'Dispatch deleted successfully'
    ]);
}

    // Search drivers by name (autocomplete)
    public function searchDrivers(Request $request)
    {
        $query = $request->get('query', '');
        
        $drivers = \App\Models\Driver::where('fname', 'LIKE', "%{$query}%")
            ->orWhere('lname', 'LIKE', "%{$query}%")
            ->select('driver_id', 'fname', 'lname')
            ->limit(10)
            ->get()
            ->map(function($driver) {
                return [
                    'driver_id' => $driver->driver_id,
                    'full_name' => $driver->fname . ' ' . $driver->lname
                ];
            });
        
        return response()->json($drivers);
    }

    // Get available trucks (dropdown)
    public function getAvailableTrucks()
    {
        $trucks = \App\Models\Truck::where('status', 'Available')
            ->select('truck_id', 'plate_no', 'description', 'status')
            ->get()
            ->map(function($truck) {
                return [
                    'truck_id' => $truck->truck_id,
                    'display_name' => $truck->plate_no . ' - ' . $truck->description
                ];
            });
        
        return response()->json($trucks);
    }
}