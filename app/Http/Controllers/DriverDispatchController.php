<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\SipaDetail;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\TripDetail;
use Illuminate\Http\Request;
use App\Models\Sipa;

class DriverDispatchController extends Controller
{
    public function index(Request $request)
    {
        // Get the logged-in driver's ID from session
        $driverId = session('driver_id');
        
        // If no driver is logged in, redirect to login
        if (!$driverId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Get status filter from URL query parameter
        $statusFilter = $request->get('status'); // "completed" or "pending"

        // Fetch dispatches assigned to this specific driver
        $allDispatches = Dispatch::where('driver_id', $driverId)
            ->where('is_archived', false)
            ->with(['tripDetails'])
            ->get();

        // Apply filtering based on trip verification status
        if ($statusFilter === 'completed') {
            // Show only dispatches where ALL trips are verified
            $dispatches = $allDispatches->filter(function($dispatch) {
                $trips = $dispatch->tripDetails;
                if ($trips->isEmpty()) {
                    return false; // No trips = not completed
                }
                // All trips must be verified
                return $trips->every(function($trip) {
                    return $trip->is_verified == 1;
                });
            });
        } elseif ($statusFilter === 'pending') {
            // Show only dispatches with at least one pending trip
            $dispatches = $allDispatches->filter(function($dispatch) {
                $trips = $dispatch->tripDetails;
                if ($trips->isEmpty()) {
                    return true; // No trips = pending
                }
                // At least one trip must be pending
                return $trips->contains(function($trip) {
                    return $trip->is_verified == 0;
                });
            });
        } else {
            // Show all dispatches (no filter)
            $dispatches = $allDispatches;
        }

        // Map the filtered dispatches to the format needed by the view
        $dispatches = $dispatches->map(function($dispatch) {
            // Get driver full name
            $driver = Driver::where('driver_id', $dispatch->driver_id)->first();
            
            // Get truck details
            $truck = Truck::where('truck_id', $dispatch->truck_id)->first();

            // Get SIPA details (route info)
            $sipaDetail = SipaDetail::where('sipa_id', $dispatch->sipa_id)->first();
            $type = Sipa::where('sipa_id', $dispatch->sipa_id)->value('type');
            
            // Calculate trip statistics
            $tripDetails = $dispatch->tripDetails;
            $totalTrips = $tripDetails->count();
            $completedTrips = $tripDetails->where('is_verified', 1)->count();
            $pendingTrips = $tripDetails->where('is_verified', 0)->count();
            
            // Determine overall status
            if ($totalTrips > 0 && $completedTrips === $totalTrips) {
                $overallStatus = 'completed';
            } elseif ($completedTrips > 0) {
                $overallStatus = 'in_progress';
            } else {
                $overallStatus = 'pending';
            }
            
            return (object)[
                'id' => $dispatch->dispatch_id,
                'dispatch_id' => $dispatch->dispatch_id, 
                'driver_name' => $driver ? trim($driver->fname . ' ' . ($driver->mname ? $driver->mname . ' ' : '') . $driver->lname) : 'N/A',
                'truck_name' => $truck ? $truck->plate_no : 'N/A',
                'from' => $sipaDetail ? $sipaDetail->route_from : 'N/A',
                'to' => $sipaDetail ? $sipaDetail->route_to : 'N/A',
                'type' => $type ?? 'N/A',
                'status' => $overallStatus,
                'total_trips' => $totalTrips,
                'completed_trips' => $completedTrips,
                'pending_trips' => $pendingTrips,
            ];
        })->values(); // Reset array keys after filtering

        return view('driver.dispatches', [
            'dispatches' => $dispatches,
            'statusFilter' => $statusFilter
        ]);
    }
}

