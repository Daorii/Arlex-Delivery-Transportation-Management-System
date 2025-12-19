<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Dispatch;  
use App\Models\DriverCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $statusFilter = $request->get('status'); // NEW: Get status filter
    $perPage = 10;

    // Build the query for non-archived drivers
    $query = Driver::where('is_archived', false);

    // ✅ NEW: Apply status filter
    if ($statusFilter) {
        $query->where('status', $statusFilter);
    }

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('fname', 'like', "%{$search}%")
              ->orWhere('mname', 'like', "%{$search}%")
              ->orWhere('lname', 'like', "%{$search}%")
              ->orWhere('license_no', 'like', "%{$search}%")
              ->orWhere('contact_number', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%");
        });
    }

    // Get paginated results
    $drivers = $query->orderBy('driver_id', 'desc')->paginate($perPage);

    // Get statistics from all non-archived drivers (not just current page)
    $allDrivers = Driver::where('is_archived', false)->get();

    return view('TD.drivers', [
        'drivers' => $drivers,
        'totalDrivers' => $allDrivers->count(),
        'activeDrivers' => $allDrivers->where('status', 'Active')->count(),
        'onTripDrivers' => $allDrivers->where('status', 'On Trip')->count(),
        'inactiveDrivers' => $allDrivers->where('status', 'Inactive')->count(),
        'search' => $search,
        'statusFilter' => $statusFilter, // ✅ NEW: Pass to view
    ]);
}

    // NEW: Show archived drivers
    public function archived()
    {
        $archivedDrivers = Driver::where('is_archived', true)->get();

        return view('TD.drivers_archived', [
            'archivedDrivers' => $archivedDrivers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:50',
            'mname' => 'nullable|string|max:50',
            'lname' => 'required|string|max:50',
            'license_no' => 'required|string|max:50',
            'contact_number' => 'required|string|max:50',
            'username' => 'required|string|max:50|unique:drivers,username',
            'password' => 'required|string|min:6',
            'status'   => 'required|string|in:Active,Inactive,On Trip',
        ]);

        Driver::create([
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'license_no' => $request->license_no,
            'contact_number' => $request->contact_number,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'is_archived' => false,
        ]);

        return redirect()->back()->with('success', 'Driver added successfully!');
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'fname' => 'required|string|max:50',
            'mname' => 'nullable|string|max:50',
            'lname' => 'required|string|max:50',
            'license_no' => 'required|string|max:50',
            'contact_number' => 'required|string|max:50',
            'status'   => 'required|string|in:Active,Inactive,On Trip',
        ]);

        $driver->update([
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'license_no' => $request->license_no,
            'contact_number' => $request->contact_number,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Driver updated successfully!');
    }

    // Archive driver
    public function archive($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->is_archived = true;
        $driver->save();

        return redirect()->back()->with('success', 'Driver archived successfully!');
    }

    // Restore driver
    public function restore($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->is_archived = false;
        $driver->save();

        return redirect()->back()->with('success', 'Driver restored successfully!');
    }

    // Permanent delete
    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->back()->with('success', 'Driver permanently deleted!');
    }

    // Driver Profile Methods
    public function profile()
    {
        $driverId = session('driver_id');
        
        if (!$driverId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }
        
        $driver = Driver::find($driverId);
        
        if (!$driver) {
            return redirect()->route('login')->with('error', 'Driver not found');
        }
        
        return view('driver.profile', compact('driver'));
    }

    public function updateProfile(Request $request)
    {
        try {
            $driverId = session('driver_id');
            
            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again.'
                ], 401);
            }
            
            $driver = Driver::find($driverId);
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            $rules = [
                'fname' => 'required|string|max:50',
                'mname' => 'nullable|string|max:50',
                'lname' => 'required|string|max:50',
                'username' => 'required|string|max:50|unique:drivers,username,' . $driver->driver_id . ',driver_id',
                'contact_number' => 'required|string|max:50',
                'license_no' => 'required|string|max:50',
            ];

            if ($request->filled('current_password')) {
                $rules['current_password'] = 'required';
                $rules['password'] = 'required|confirmed|min:6';
            }

            $validated = $request->validate($rules);

            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $driver->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect'
                    ], 422);
                }
            }

            $driver->update([
                'fname' => $validated['fname'],
                'mname' => $validated['mname'],
                'lname' => $validated['lname'],
                'username' => $validated['username'],
                'contact_number' => $validated['contact_number'],
                'license_no' => $validated['license_no'],
            ]);

            if ($request->filled('password')) {
                $driver->update([
                    'password' => Hash::make($request->password)
                ]);
            }
            
            session()->put('driver_name', $validated['fname'] . ' ' . $validated['lname']);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating driver profile', [
                'error' => $e->getMessage(),
                'driver_id' => session('driver_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard()
{
    $driverId = session('driver_id');
    
    if (!$driverId) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    $driver = Driver::find($driverId);

    // Get ALL dispatches for this driver
    $allDispatches = Dispatch::where('driver_id', $driverId)
        ->with(['sipa', 'truck', 'driver', 'tripDetails.sipaDetail'])
        ->orderBy('dispatch_id', 'desc')
        ->get();

    // Get RECENT dispatches (non-archived, last 3)
    $recentDispatchesData = Dispatch::where('driver_id', $driverId)
        ->where('is_archived', false)
        ->with(['sipa', 'truck', 'driver', 'tripDetails.sipaDetail'])
        ->orderBy('dispatch_id', 'desc')
        ->take(3)
        ->get();

    $recentDispatches = $recentDispatchesData->map(function ($dispatch) {
        $sipa = $dispatch->sipa;
        $tripDetail = $dispatch->tripDetails->first();
        $sipaDetail = $tripDetail ? $tripDetail->sipaDetail : null;
        
        $from = 'N/A';
        $to = 'N/A';
        $sipaNumber = 'N/A';
        $type = 'N/A';
        
        // Get SIPA info
        if ($sipa) {
            $sipaNumber = $sipa->sipa_ref_no ?? 'N/A';
            $type = $sipa->type ?? 'N/A';
        }
        
        // Get location from SipaDetail (route_from and route_to)
        if ($sipaDetail) {
            $from = $sipaDetail->route_from ?? 'N/A';
            $to = $sipaDetail->route_to ?? 'N/A';
        }
        
        return [
            'dispatch_id' => $dispatch->dispatch_id,
            'sipa_number' => $sipaNumber,
            'driver_name' => ($dispatch->driver->fname ?? '') . ' ' . ($dispatch->driver->lname ?? ''),
            'truck_name' => $dispatch->truck ? ($dispatch->truck->plate_no ?? $dispatch->truck->plate_number ?? 'N/A') : 'N/A',
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'status' => $dispatch->status ?? 'pending',
        ];
    });

    // ✅ FIX: Count TRIP DETAILS (EIRs) instead of Dispatches
    $dispatchIds = $allDispatches->pluck('dispatch_id')->toArray();
    
    // Get all trip details for this driver's dispatches
    $allTripDetails = \App\Models\TripDetail::whereIn('dispatch_id', $dispatchIds)->get();
    
    // Count trips
    $totalTrips = $allTripDetails->count(); // Total submitted EIRs
    $completedTrips = $allTripDetails->where('is_verified', 1)->count(); // Approved/Verified EIRs
    $pendingTrips = $allTripDetails->where('is_verified', 0)->count(); // Pending verification

    // Total commission earned
    $totalCommission = DriverCommission::where('driver_id', $driverId)
        ->sum('commission_amount') ?? 0;

    return view('driver.dashboard', compact(
        'driver',
        'recentDispatches',
        'totalTrips',
        'completedTrips',
        'pendingTrips',
        'totalCommission'
    ));
}
}