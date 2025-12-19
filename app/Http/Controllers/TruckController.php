<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Truck;

class TruckController extends Controller
{
    // Show all trucks (non-archived only)
    public function index(Request $request)
{
    $search = $request->get('search');
    $statusFilter = $request->get('status'); // NEW: Get status filter
    $perPage = 10;

    // Build the query for non-archived trucks
    $query = Truck::where('is_archived', false);

    // ✅ NEW: Apply status filter
    if ($statusFilter) {
        $query->where('status', $statusFilter);
    }

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('plate_no', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%");
        });
    }

    // Get paginated results
    $trucks = $query->orderBy('truck_id', 'desc')->paginate($perPage);

    // Get statistics from all non-archived trucks (not just current page)
    $allTrucks = Truck::where('is_archived', false)->get();
    $totalTrucks = $allTrucks->count();
    $availableTrucks = $allTrucks->where('status', 'Available')->count();
    $maintenanceTrucks = $allTrucks->where('status', 'Maintenance')->count();
    $onTripTrucks = $allTrucks->where('status', 'On Trip')->count();

    return view('TD.trucks', compact(
        'trucks', 
        'totalTrucks', 
        'availableTrucks', 
        'maintenanceTrucks', 
        'onTripTrucks', 
        'search',
        'statusFilter' // ✅ NEW: Pass to view
    ));
}

    // NEW: Show archived trucks
    public function archived()
    {
        $archivedTrucks = Truck::where('is_archived', true)
                               ->orderBy('truck_id', 'desc')
                               ->get();

        return view('TD.trucks_archived', [
            'archivedTrucks' => $archivedTrucks,
        ]);
    }

    // Store a new truck
    public function store(Request $request)
    {
        $request->validate([
            'plate_no' => 'required|string|unique:trucks,plate_no',
            'description' => 'required|string',
            'status' => 'required|in:Available,Maintenance,On Trip',
        ]);

        Truck::create([
            'plate_no' => $request->plate_no,
            'description' => $request->description,
            'status' => $request->status,
            'is_archived' => false,
        ]);

        return redirect()->route('trucks.index')->with('success', 'Truck added successfully.');
    }

    // Update an existing truck
    public function update(Request $request, Truck $truck)
    {
        $request->validate([
            'plate_no' => 'required|string|unique:trucks,plate_no,' . $truck->truck_id . ',truck_id',
            'status' => 'required|in:Available,Maintenance,On Trip',
            'maintenance_reason' => 'nullable|string',
        ]);

        if ($request->status === 'Maintenance') {
            $request->validate([
                'maintenance_reason' => 'required|string',
            ]);
        }

        $truck->update([
            'plate_no' => $request->plate_no,
            'status' => $request->status,
            'description' => $request->description,
            'maintenance_reason' => $request->maintenance_reason,
        ]);

        return redirect()->route('trucks.index')->with('success', 'Truck updated successfully.');
    }

    // Archive truck
    public function archive($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->is_archived = true;
        $truck->save();

        return redirect()->back()->with('success', 'Truck archived successfully!');
    }

    // Restore truck
    public function restore($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->is_archived = false;
        $truck->save();

        return redirect()->back()->with('success', 'Truck restored successfully!');
    }

    // Permanent delete
    public function destroy(Truck $truck)
    {
        $truck->delete();
        return redirect()->back()->with('success', 'Truck permanently deleted!');
    }
}