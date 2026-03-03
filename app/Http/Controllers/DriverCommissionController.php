<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Dispatch;
use App\Models\TripDetail;
use App\Models\SipaDetail;
use App\Models\DriverCommission;

class DriverCommissionController extends Controller
{
    // ==========================================================
    // DRIVER COMMISSION PAGE (main computation page)
    // ==========================================================
    public function driverCommission()
{
    $drivers = Driver::all()->map(function ($driver) {
        $dispatches = Dispatch::where('driver_id', $driver->driver_id)
            ->pluck('dispatch_id');

        // Get all saved commission dispatch IDs for this driver
        $savedCommissionDispatchIds = DriverCommission::where('driver_id', $driver->driver_id)
            ->pluck('dispatch_id')
            ->toArray();

        $approvedEirs = TripDetail::whereIn('dispatch_id', $dispatches)
            ->where('is_verified', 1)
            ->whereNotIn('dispatch_id', $savedCommissionDispatchIds) // Exclude already saved
            ->get()
                ->map(function ($trip) {
                    $sipaDetail = SipaDetail::find($trip->sipa_detail_id);
                    $sipa = $sipaDetail->sipa ?? null;

                    return [
                        'detail_id'     => $trip->detail_id,
                        'dispatch_id'   => $trip->dispatch_id,
                        'container_no'  => $trip->container_no,
                        'eir_no'        => $trip->eir_no,
                        'delivery_date' => $trip->delivery_date,
                        'size'          => $sipaDetail->size ?? 'N/A',
                        'type'          => $sipa->type ?? 'N/A',
                        'price'         => $sipaDetail->price ?? 0,
                    ];
                });

            return [
                'driver_id'      => $driver->driver_id,
                'driver_name'    => $driver->fname . ' ' . $driver->lname,
                'approved_count' => $approvedEirs->count(),
                'approved_eirs'  => $approvedEirs
            ];
        });

        return view('TD.DriverCommission', [
            'drivers' => $drivers
        ]);
    }

    // ==========================================================
    // COMMISSION RECORDS PAGE
    // ==========================================================
    public function records()
{
    // Get all drivers with their commission count
    $drivers = \DB::table('drivers')
        ->leftJoin('drivercommissions', 'drivers.driver_id', '=', 'drivercommissions.driver_id')
        ->select(
            'drivers.driver_id',
            \DB::raw("CONCAT(drivers.fname, ' ', drivers.lname) as driver_name"),
            \DB::raw('COUNT(drivercommissions.commission_id) as commission_count')
        )
        ->groupBy('drivers.driver_id', 'drivers.fname', 'drivers.lname')
        ->get();

    return view('TD.Commission', compact('drivers'));
}


public function getClientDrivers($clientId)
{
    try {
        // Get all drivers who have commissions for dispatches linked to this client's SIPAs
        $drivers = \DB::table('drivers')
            ->join('dispatches', 'drivers.driver_id', '=', 'dispatches.driver_id')
            ->join('drivercommissions', 'dispatches.dispatch_id', '=', 'drivercommissions.dispatch_id')
            ->join('siparequest', 'dispatches.sipa_id', '=', 'siparequest.sipa_id')
            ->where('siparequest.client_id', $clientId)
            ->select(
                'drivers.driver_id',
                \DB::raw("CONCAT(drivers.fname, ' ', drivers.lname) as driver_name"),
                \DB::raw('COUNT(DISTINCT drivercommissions.commission_id) as commission_count')
            )
            ->groupBy('drivers.driver_id', 'drivers.fname', 'drivers.lname')
            ->get();

        return response()->json([
            'success' => true,
            'drivers' => $drivers
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getClientDrivers', [
            'client_id' => $clientId,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}




    // ==========================================================
    // FETCH APPROVED TRIPS (AJAX)
    // ==========================================================
    public function fetchApproved(Request $request)
    {
        $driverId  = $request->driver_id;
        $from      = $request->from;
        $to        = $request->to;
        $reference = $request->reference;

        if (!$driverId || !$from || !$to) {
            return response()->json(['success' => false, 'message' => 'Missing required fields']);
        }

        $dispatches = Dispatch::where('driver_id', $driverId)->pluck('dispatch_id');

        $approvedEirs = TripDetail::whereIn('dispatch_id', $dispatches)
            ->where('is_verified', 1)
            ->when($reference, function ($query, $ref) {
                $query->where(function ($q) use ($ref) {
                    $q->where('eir_no', 'like', "%$ref%")
                        ->orWhere('dispatch_id', 'like', "%$ref%")
                        ->orWhere('container_no', 'like', "%$ref%");
                });
            })
            ->whereBetween('delivery_date', [$from, $to])
            ->get()
            ->map(function ($trip) {
                $sipaDetail = SipaDetail::find($trip->sipa_detail_id);
                $sipa = $sipaDetail->sipa ?? null;

                return [
                    'detail_id'     => $trip->detail_id,
                    'dispatch_id'   => $trip->dispatch_id,
                    'container_no'  => $trip->container_no,
                    'eir_no'        => $trip->eir_no,
                    'delivery_date' => $trip->delivery_date,
                    'size'          => $sipaDetail->size ?? 'N/A',
                    'type'          => $sipa->type ?? 'N/A',
                    'price'         => $sipaDetail->price ?? 0,
                ];
            });

        return response()->json(['success' => true, 'data' => $approvedEirs]);
    }

    // ==========================================================
    // SAVE COMMISSION
    // ==========================================================
    public function saveCommission(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|integer',
            'items'     => 'required|array',
            'rate'      => 'required|numeric',
            'from'      => 'required|date',
            'to'        => 'required|date',
        ]);

        foreach ($request->items as $item) {

            $totalTripAmount  = $item['price'] ?? 0;
            $commissionAmount = $totalTripAmount * ($request->rate / 100);

            DriverCommission::create([
                'driver_id'          => $request->driver_id,
                'dispatch_id'        => $item['dispatch_id'],
                'total_trip_amount'  => $totalTripAmount,
                'commission_rate'    => $request->rate,
                'commission_amount'  => $commissionAmount,
                'week_period_text'   => $request->from . ' to ' . $request->to,
                'status'             => 'Pending',
                'paid_at'            => null,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Commission saved successfully']);
    }

    // ==========================================================
    // FETCH COMMISSIONS FOR DRIVER (AJAX)
    // ==========================================================
    public function viewDriverCommissions($driverId)
{
    $commissions = DriverCommission::where('driver_id', $driverId)
        ->orderBy('commission_id', 'desc')
        ->get()
        ->map(function ($c) {
            return [
                'commission_id'     => $c->commission_id,
                'driver_id'         => $c->driver_id,
                'dispatch_id'       => $c->dispatch_id,
                'total_trip_amount' => $c->total_trip_amount,
                'commission_rate'   => $c->commission_rate,
                'commission_amount' => $c->commission_amount,
                'week_period_text'  => $c->week_period_text,
                'status'            => $c->status,
                'paid_at'           => $c->paid_at, // ADD THIS LINE
            ];
        });

    return response()->json(['success' => true, 'data' => $commissions]);
}

    // ==========================================================
    // UPDATE SINGLE COMMISSION STATUS (AJAX)
    // ==========================================================
    public function updateStatus(Request $request, $commissionId)
{
    $request->validate([
        'status' => 'required|string|in:Pending,Paid'
    ]);

    $commission = DriverCommission::find($commissionId);

    if (!$commission) {
        return response()->json(['success' => false, 'message' => 'Commission not found']);
    }

    $commission->status = $request->status;
    
    // Set paid_at when marking as Paid
    if ($request->status === 'Paid') {
        $commission->paid_at = now();
    }
    
    $commission->save();

    return response()->json(['success' => true, 'message' => 'Status updated']);
}

    // ==========================================================
    // NEW FEATURE: RELEASE ALL PENDING COMMISSIONS FOR DRIVER
    // ==========================================================
    public function releaseAllPendingForDriver($driverId)
    {
        $updated = DriverCommission::where('driver_id', $driverId)
            ->where('status', 'Pending')
            ->update([
                'status'  => 'Paid',
                'paid_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
            'message' => 'All pending commissions have been released.'
        ]);
    }
    
    public function driverCommissionsPage(Request $request)
{
    $driverId = session('driver_id');
    
    if (!$driverId) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    $search = $request->get('search');
    $perPage = 10;

    // Build the query
    $query = DriverCommission::where('driver_id', $driverId);

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('dispatch_id', 'like', "%{$search}%")
              ->orWhere('week_period_text', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('commission_amount', 'like', "%{$search}%")
              ->orWhere('commission_rate', 'like', "%{$search}%")
              ->orWhere('total_trip_amount', 'like', "%{$search}%");
        });
    }

    // Get paginated results
    $commissions = $query->orderBy('commission_id', 'desc')->paginate($perPage);

    // Get all commissions for summary cards (not affected by pagination)
    $allCommissions = DriverCommission::where('driver_id', $driverId)->get();

    return view('driver.commission', [
        'commissions' => $commissions,
        'allCommissions' => $allCommissions,
        'search' => $search
    ]);
}

public function fetchDriverCommissions()
{
    $driverId = session('driver_id');

    if (!$driverId) {
        return response()->json(['success' => false, 'message' => 'Not logged in']);
    }

    $commissions = DriverCommission::where('driver_id', $driverId)
        ->orderBy('commission_id', 'desc')
        ->get();

    return response()->json(['success' => true, 'data' => $commissions]);
}


// Add this new method in DriverCommissionController
// Replace your getDriverWeeklyPeriods method with this:

// 1. In DriverCommissionController.php, replace getDriverWeeklyPeriods:

public function getDriverWeeklyPeriods($driverId)
{
    try {
        $dispatches = Dispatch::where('driver_id', $driverId)->pluck('dispatch_id');

        $trips = TripDetail::whereIn('dispatch_id', $dispatches)
            ->where('is_verified', 1)
            ->whereNotNull('delivery_date')
            ->orderBy('delivery_date', 'desc')
            ->get();

        // Get already saved commission periods for this driver
        $savedPeriods = DriverCommission::where('driver_id', $driverId)
            ->get()
            ->map(function($commission) {
                // Extract week_start and week_end from week_period_text
                // Format: "2024-12-02 to 2024-12-08"
                $parts = explode(' to ', $commission->week_period_text);
                return [
                    'week_start' => trim($parts[0] ?? ''),
                    'week_end' => trim($parts[1] ?? '')
                ];
            })
            ->filter(function($period) {
                return !empty($period['week_start']) && !empty($period['week_end']);
            });

        $weeklyPeriods = [];
        
        foreach ($trips as $trip) {
            $deliveryDate = \Carbon\Carbon::parse($trip->delivery_date);
            
            $weekStart = $deliveryDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $weekEnd = $deliveryDate->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
            
            $weekKey = $weekStart->format('Y-m-d');
            
            // Check if this period already has a saved commission
            $alreadySaved = $savedPeriods->contains(function($saved) use ($weekStart, $weekEnd) {
                return $saved['week_start'] === $weekStart->format('Y-m-d') 
                    && $saved['week_end'] === $weekEnd->format('Y-m-d');
            });
            
            // Skip this week if already saved
            if ($alreadySaved) {
                continue;
            }
            
            if (!isset($weeklyPeriods[$weekKey])) {
                $weeklyPeriods[$weekKey] = [
                    'week_start' => $weekStart->format('Y-m-d'),
                    'week_end' => $weekEnd->format('Y-m-d'),
                    'week_label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d, Y'),
                    'trip_count' => 0,
                    'total_amount' => 0,
                    'trips' => []
                ];
            }
            
            $sipaDetail = SipaDetail::find($trip->sipa_detail_id);
            $sipa = $sipaDetail->sipa ?? null;
            $price = $sipaDetail->price ?? 0;
            
            $weeklyPeriods[$weekKey]['trip_count']++;
            $weeklyPeriods[$weekKey]['total_amount'] += $price;
            $weeklyPeriods[$weekKey]['trips'][] = [
                'detail_id' => $trip->detail_id,
                'dispatch_id' => $trip->dispatch_id,
                'container_no' => $trip->container_no,
                'eir_no' => $trip->eir_no,
                'delivery_date' => $trip->delivery_date,
                'size' => $sipaDetail->size ?? 'N/A',
                'type' => $sipa->type ?? 'N/A',
                'price' => $price,
            ];
        }

        $periods = array_values($weeklyPeriods);
        usort($periods, function($a, $b) {
            return strtotime($b['week_start']) - strtotime($a['week_start']);
        });

        return response()->json([
            'success' => true,
            'periods' => $periods
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

}


