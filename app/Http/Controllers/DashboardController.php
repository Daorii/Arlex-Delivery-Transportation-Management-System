<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Dispatch;
use App\Models\TripDetail;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Clients (excluding archived)
        $totalClients = Client::where('is_archived', false)->count();

        // Active Trips (non-archived dispatches that are not completed)
        $activeTrips = Dispatch::where('is_archived', false)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        // Pending Invoices (invoices with status 'Sent' or 'Partially Paid')
        $pendingInvoices = Invoice::where('is_archived', false)
            ->whereIn('invoice_status', ['Sent', 'Partially Paid'])
            ->count();

        // Total Revenue (sum of all fully paid invoices)
        $totalRevenue = Invoice::where('is_archived', false)
            ->where('invoice_status', 'Fully Paid')
            ->sum('net_total');

        // Trucks Availability (excluding archived)
        $availableTrucks = Truck::where('is_archived', false)
            ->where('status', 'Available')
            ->count();
        
        $trucksOnTrip = Truck::where('is_archived', false)
            ->where('status', 'On Trip')
            ->count();
        
        $trucksInMaintenance = Truck::where('is_archived', false)
            ->where('status', 'Maintenance')
            ->count();
        
        $totalTrucks = Truck::where('is_archived', false)->count();

        // Drivers Availability (excluding archived)
        $availableDrivers = Driver::where('is_archived', false)
            ->where('status', 'Active')
            ->count();
        
        $driversOnTrip = Driver::where('is_archived', false)
            ->where('status', 'On Trip')
            ->count();
        
        $inactiveDrivers = Driver::where('is_archived', false)
            ->where('status', 'Inactive')
            ->count();
        
        $totalDrivers = Driver::where('is_archived', false)->count();

        // Top Client (most trips) - excluding archived
        $topClientData = DB::table('dispatches')
            ->join('siparequest', 'dispatches.sipa_id', '=', 'siparequest.sipa_id')
            ->join('clients', 'siparequest.client_id', '=', 'clients.client_id')
            ->where('dispatches.is_archived', false)
            ->where('clients.is_archived', false)
            ->select('clients.company_name', DB::raw('COUNT(dispatches.dispatch_id) as trip_count'))
            ->groupBy('clients.client_id', 'clients.company_name')
            ->orderBy('trip_count', 'desc')
            ->first();

        $topClient = $topClientData ? $topClientData->company_name : 'N/A';
        $topClientTrips = $topClientData ? $topClientData->trip_count : 0;

        // Top Driver (most trips) - excluding archived
        $topDriverData = DB::table('dispatches')
            ->join('drivers', 'dispatches.driver_id', '=', 'drivers.driver_id')
            ->where('dispatches.is_archived', false)
            ->where('drivers.is_archived', false)
            ->select(
                DB::raw("TRIM(CONCAT(drivers.fname, ' ', COALESCE(drivers.mname, ''), ' ', drivers.lname)) as driver_name"),
                DB::raw('COUNT(dispatches.dispatch_id) as trip_count')
            )
            ->groupBy('drivers.driver_id', 'drivers.fname', 'drivers.mname', 'drivers.lname')
            ->orderBy('trip_count', 'desc')
            ->first();

        $topDriver = $topDriverData ? $topDriverData->driver_name : 'N/A';
        $topDriverTrips = $topDriverData ? $topDriverData->trip_count : 0;

        // Maintenance Alerts - excluding archived
        $maintenanceTrucks = Truck::where('is_archived', false)
            ->where('status', 'Maintenance')
            ->get()
            ->map(function($truck) {
                return [
                    'plate_no' => $truck->plate_no,
                    'issue' => $truck->maintenance_reason ?? 'Routine maintenance',
                    'status' => 'Under Maintenance'
                ];
            });

        return view('dashboard', compact(
            'totalClients',
            'activeTrips',
            'pendingInvoices',
            'totalRevenue',
            'availableTrucks',
            'trucksOnTrip',
            'trucksInMaintenance',
            'totalTrucks',
            'availableDrivers',
            'driversOnTrip',
            'inactiveDrivers',
            'totalDrivers',
            'topClient',
            'topClientTrips',
            'topDriver',
            'topDriverTrips',
            'maintenanceTrucks'
        ));
    }
}