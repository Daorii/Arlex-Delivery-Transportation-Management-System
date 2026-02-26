<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;
use App\Models\Sipa;
use App\Models\TripDetail;
use App\Models\TransportOrder;
use App\Models\Invoice;
use App\Models\Dispatch;

class ReportsAndAnalyticsController extends Controller
{
    public function index(Request $request)
{
    // Get date filters
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    
    // Stats Cards - Apply date filter
    $sipaQuery = Sipa::where('is_archived', false);
    if ($fromDate && $toDate) {
        $sipaQuery->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);
    }
    $totalSipa = $sipaQuery->count();
    
    $tripsQuery = TripDetail::whereHas('dispatch', function($query) {
        $query->where('is_archived', false);
    });
    if ($fromDate && $toDate) {
        $tripsQuery->whereBetween(DB::raw('DATE(delivery_date)'), [$fromDate, $toDate]);
    }
    $totalTrips = $tripsQuery->count();
    
    // Transport Orders - Count UNIQUE with date filter
    $completedQuery = DB::table('transport_orders')
        ->where('is_archived', false)
        ->where('verification_status', 'Approved');
    if ($fromDate && $toDate) {
        $completedQuery->whereBetween(DB::raw('DATE(verified_at)'), [$fromDate, $toDate]);
    }
    $completedOrders = $completedQuery->distinct('to_ref_no')->count('to_ref_no');
    
    $pendingQuery = DB::table('transport_orders')
        ->where('is_archived', false)
        ->where('verification_status', 'Pending');
    if ($fromDate && $toDate) {
        $pendingQuery->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);
    }
    $pendingOrders = $pendingQuery->distinct('to_ref_no')->count('to_ref_no');
    
    // Total Revenue - with date filter
    $revenueQuery = Invoice::where('is_archived', false)
        ->where('invoice_status', 'Fully Paid');
    if ($fromDate && $toDate) {
        $revenueQuery->whereBetween(DB::raw('DATE(invoice_date)'), [$fromDate, $toDate]);
    }
    $totalRevenue = $revenueQuery->sum('net_total');

    // Pass date filters to chart methods
    $revenueTrendData = $this->getRevenueTrendData($fromDate, $toDate);
    $weeklyTripsData = $this->getWeeklyTripsData($fromDate, $toDate);
    $ordersStatusData = $this->getOrdersStatusData($fromDate, $toDate);

    // Recent Transport Orders with date filter
    $recentOrdersQuery = DB::table('transport_orders')
        ->where('is_archived', false);
    if ($fromDate && $toDate) {
        $recentOrdersQuery->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);
    }
    $recentOrdersRaw = $recentOrdersQuery->orderBy('to_id', 'desc')->get();

    $groupedOrders = [];
    foreach ($recentOrdersRaw as $order) {
        if (!isset($groupedOrders[$order->to_ref_no])) {
            $groupedOrders[$order->to_ref_no] = [
                'id' => $order->to_id,
                'ref' => $order->to_ref_no,
                'from' => $order->depot_from ?? 'N/A',
                'to' => $order->depot_to ?? 'N/A',
                'status' => $order->verification_status ?? 'Pending'
            ];
        }
    }
    $recentOrders = array_slice(array_values($groupedOrders), 0, 5);

    // Recent Invoices with date filter
    $invoicesQuery = Invoice::where('is_archived', false)
        ->with('transportOrder.billing.client');
    if ($fromDate && $toDate) {
        $invoicesQuery->whereBetween(DB::raw('DATE(invoice_date)'), [$fromDate, $toDate]);
    }
    $recentInvoices = $invoicesQuery->orderBy('invoice_id', 'desc')
        ->limit(5)
        ->get()
        ->map(function($invoice) {
            $clientName = 'N/A';
            if ($invoice->transportOrder && 
                $invoice->transportOrder->billing && 
                $invoice->transportOrder->billing->client) {
                $clientName = $invoice->transportOrder->billing->client->company_name;
            }
            
            $status = strtolower($invoice->invoice_status ?? 'pending');
            if ($status === 'fully paid') {
                $status = 'paid';
            } elseif ($status === 'sent' || $status === 'partially paid') {
                $status = 'pending';
            }
            
            return [
                'id' => $invoice->invoice_id,
                'no' => $invoice->invoice_no ?? 'INV-' . $invoice->invoice_id,
                'client' => $clientName,
                'total' => $invoice->net_total ?? 0,
                'status' => $status
            ];
        });

    // Recent Trips with date filter - FIXED VERSION
    $tripsQuery = Dispatch::where('is_archived', false)
        ->with(['driver', 'truck', 'tripDetails'])
        ->whereHas('driver', function($query) {
            $query->where('is_archived', false);
        })
        ->whereHas('truck', function($query) {
            $query->where('is_archived', false);
        });
        
    if ($fromDate && $toDate) {
        // Filter by trip delivery date instead of dispatch created_at
        $tripsQuery->whereHas('tripDetails', function($query) use ($fromDate, $toDate) {
            $query->whereBetween(DB::raw('DATE(delivery_date)'), [$fromDate, $toDate]);
        });
    }

    $recentTrips = $tripsQuery->orderBy('dispatch_id', 'desc')
        ->limit(5)
        ->get()
        ->map(function($dispatch) {
            $driver = $dispatch->driver;
            $truck = $dispatch->truck;
            $tripDetail = $dispatch->tripDetails->first();
            
            $status = 'Scheduled';
            if ($tripDetail && $tripDetail->is_verified == 1) {
                $status = 'Completed';
            } elseif ($dispatch->status === 'in_progress') {
                $status = 'In Transit';
            }
            
            return [
                'id' => $dispatch->dispatch_id,
                'vehicle' => $truck ? $truck->plate_no : 'N/A',
                'driver' => $driver ? trim($driver->fname . ' ' . ($driver->mname ?? '') . ' ' . $driver->lname) : 'N/A',
                'status' => $status
            ];
        });

    return view('RA.ReportsAndAnalytics', compact(
        'totalSipa',
        'totalTrips',
        'completedOrders',
        'pendingOrders',
        'totalRevenue',
        'revenueTrendData',
        'weeklyTripsData',
        'ordersStatusData',
        'recentOrders',
        'recentInvoices',
        'recentTrips'
    ));
}
   

    private function getRevenueTrendData($fromDate = null, $toDate = null)
{
    $data = [];
    
    // Transport Orders revenue query
    $transportQuery = DB::table('transport_orders')
        ->select(
            DB::raw('YEAR(verified_at) as year'),
            DB::raw('QUARTER(verified_at) as quarter'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->where('is_archived', false)
        ->where('verification_status', 'Approved')
        ->whereNotNull('verified_at');
    
    if ($fromDate && $toDate) {
        $transportQuery->whereBetween(DB::raw('DATE(verified_at)'), [$fromDate, $toDate]);
    }
    
    $transportRevenue = $transportQuery->groupBy('year', 'quarter')
        ->orderBy('year')
        ->orderBy('quarter')
        ->get();

    foreach ($transportRevenue as $order) {
        $data[] = [
            'Period' => $order->year . ' Q' . $order->quarter,
            'Category' => 'Transport Orders',
            'Revenue' => (float)($order->revenue ?? 0)
        ];
    }

    // Invoices revenue query
    $invoiceQuery = DB::table('invoices')
        ->select(
            DB::raw('YEAR(invoice_date) as year'),
            DB::raw('QUARTER(invoice_date) as quarter'),
            DB::raw('SUM(net_total) as revenue')
        )
        ->where('is_archived', false)
        ->where('invoice_status', 'Fully Paid')
        ->whereNotNull('invoice_date');
    
    if ($fromDate && $toDate) {
        $invoiceQuery->whereBetween(DB::raw('DATE(invoice_date)'), [$fromDate, $toDate]);
    }
    
    $invoiceRevenue = $invoiceQuery->groupBy('year', 'quarter')
        ->orderBy('year')
        ->orderBy('quarter')
        ->get();

    foreach ($invoiceRevenue as $invoice) {
        $data[] = [
            'Period' => $invoice->year . ' Q' . $invoice->quarter,
            'Category' => 'Invoices',
            'Revenue' => (float)($invoice->revenue ?? 0)
        ];
    }

    // If no data and no filter, show sample data
    if (empty($data) && !$fromDate && !$toDate) {
        $currentYear = date('Y');
        $currentQuarter = ceil(date('n') / 3);
        
        for ($i = 7; $i >= 0; $i--) {
            $year = $currentYear - floor($i / 4);
            $quarter = (($currentQuarter - ($i % 4)) + 4) % 4;
            if ($quarter === 0) $quarter = 4;
            
            $period = $year . ' Q' . $quarter;
            $data[] = ['Period' => $period, 'Category' => 'Transport Orders', 'Revenue' => 0];
            $data[] = ['Period' => $period, 'Category' => 'Invoices', 'Revenue' => 0];
        }
    }

    // Keep test data only if no filter applied
    if (!$fromDate && !$toDate && count($data) < 16) {
        $testData = [
            // 2023 Q1 - Starting point
            ['Period' => '2023 Q1', 'Category' => 'Transport Orders', 'Revenue' => 105000],
            ['Period' => '2023 Q1', 'Category' => 'Invoices', 'Revenue' => 145000],
            ['Period' => '2023 Q1', 'Category' => 'Fuel & Maintenance', 'Revenue' => 125000],
            ['Period' => '2023 Q1', 'Category' => 'Additional Services', 'Revenue' => 95000],
            
            ['Period' => '2023 Q2', 'Category' => 'Transport Orders', 'Revenue' => 95000],
            ['Period' => '2023 Q2', 'Category' => 'Invoices', 'Revenue' => 148000],
            ['Period' => '2023 Q2', 'Category' => 'Fuel & Maintenance', 'Revenue' => 165000],
            ['Period' => '2023 Q2', 'Category' => 'Additional Services', 'Revenue' => 110000],
            
            ['Period' => '2023 Q3', 'Category' => 'Transport Orders', 'Revenue' => 132000],
            ['Period' => '2023 Q3', 'Category' => 'Invoices', 'Revenue' => 185000],
            ['Period' => '2023 Q3', 'Category' => 'Fuel & Maintenance', 'Revenue' => 142000],
            ['Period' => '2023 Q3', 'Category' => 'Additional Services', 'Revenue' => 108000],
            
            ['Period' => '2023 Q4', 'Category' => 'Transport Orders', 'Revenue' => 158000],
            ['Period' => '2023 Q4', 'Category' => 'Invoices', 'Revenue' => 215000],
            ['Period' => '2023 Q4', 'Category' => 'Fuel & Maintenance', 'Revenue' => 138000],
            ['Period' => '2023 Q4', 'Category' => 'Additional Services', 'Revenue' => 88000],
            
            ['Period' => '2024 Q1', 'Category' => 'Transport Orders', 'Revenue' => 135000],
            ['Period' => '2024 Q1', 'Category' => 'Invoices', 'Revenue' => 195000],
            ['Period' => '2024 Q1', 'Category' => 'Fuel & Maintenance', 'Revenue' => 158000],
            ['Period' => '2024 Q1', 'Category' => 'Additional Services', 'Revenue' => 135000],
            
            ['Period' => '2024 Q2', 'Category' => 'Transport Orders', 'Revenue' => 168000],
            ['Period' => '2024 Q2', 'Category' => 'Invoices', 'Revenue' => 245000],
            ['Period' => '2024 Q2', 'Category' => 'Fuel & Maintenance', 'Revenue' => 172000],
            ['Period' => '2024 Q2', 'Category' => 'Additional Services', 'Revenue' => 132000],
            
            ['Period' => '2024 Q3', 'Category' => 'Transport Orders', 'Revenue' => 192000],
            ['Period' => '2024 Q3', 'Category' => 'Invoices', 'Revenue' => 275000],
            ['Period' => '2024 Q3', 'Category' => 'Fuel & Maintenance', 'Revenue' => 185000],
            ['Period' => '2024 Q3', 'Category' => 'Additional Services', 'Revenue' => 115000],
            
            ['Period' => '2024 Q4', 'Category' => 'Transport Orders', 'Revenue' => 215000],
            ['Period' => '2024 Q4', 'Category' => 'Invoices', 'Revenue' => 280000],
            ['Period' => '2024 Q4', 'Category' => 'Fuel & Maintenance', 'Revenue' => 165000],
            ['Period' => '2024 Q4', 'Category' => 'Additional Services', 'Revenue' => 145000],
            
            ['Period' => '2025 Q1', 'Category' => 'Transport Orders', 'Revenue' => 195000],
            ['Period' => '2025 Q1', 'Category' => 'Invoices', 'Revenue' => 305000],
            ['Period' => '2025 Q1', 'Category' => 'Fuel & Maintenance', 'Revenue' => 195000],
            ['Period' => '2025 Q1', 'Category' => 'Additional Services', 'Revenue' => 168000],
            
            ['Period' => '2025 Q2', 'Category' => 'Transport Orders', 'Revenue' => 225000],
            ['Period' => '2025 Q2', 'Category' => 'Invoices', 'Revenue' => 340000],
            ['Period' => '2025 Q2', 'Category' => 'Fuel & Maintenance', 'Revenue' => 192000],
            ['Period' => '2025 Q2', 'Category' => 'Additional Services', 'Revenue' => 195000],
            
            ['Period' => '2025 Q3', 'Category' => 'Transport Orders', 'Revenue' => 248000],
            ['Period' => '2025 Q3', 'Category' => 'Invoices', 'Revenue' => 365000],
            ['Period' => '2025 Q3', 'Category' => 'Fuel & Maintenance', 'Revenue' => 175000],
            ['Period' => '2025 Q3', 'Category' => 'Additional Services', 'Revenue' => 205000],
        ];
        
        $data = array_merge($testData, $data);
    }

    return $data;
}

    private function getWeeklyTripsData($fromDate = null, $toDate = null)
{
    $tripsQuery = DB::table('tripdetails')
        ->join('dispatches', 'tripdetails.dispatch_id', '=', 'dispatches.dispatch_id')
        ->where('dispatches.is_archived', false)
        ->select(
            DB::raw('DAYNAME(tripdetails.delivery_date) as day'),
            DB::raw('COUNT(*) as trips')
        )
        ->whereNotNull('tripdetails.delivery_date')
        ->groupBy('day');
    
    if ($fromDate && $toDate) {
        $tripsQuery->whereBetween(DB::raw('DATE(tripdetails.delivery_date)'), [$fromDate, $toDate]);
    } else {
        $tripsQuery->where('tripdetails.delivery_date', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 7 DAY)'));
    }
    
    $trips = $tripsQuery->get();

    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $data = [];

    foreach ($daysOfWeek as $day) {
        $dayData = $trips->firstWhere('day', $day);
        $data[] = [
            'day' => substr($day, 0, 3),
            'trips' => $dayData ? (int)$dayData->trips : 0
        ];
    }

    return $data;
}

    private function getOrdersStatusData($fromDate = null, $toDate = null)
{
    $ordersQuery = DB::table('transport_orders')
        ->select(
            DB::raw('YEAR(verified_at) as year'),
            DB::raw('QUARTER(verified_at) as quarter'),
            'verification_status',
            DB::raw('COUNT(DISTINCT to_ref_no) as count')
        )
        ->where('is_archived', false)
        ->whereNotNull('verified_at')
        ->groupBy('year', 'quarter', 'verification_status')
        ->orderBy('year')
        ->orderBy('quarter');
    
    if ($fromDate && $toDate) {
        $ordersQuery->whereBetween(DB::raw('DATE(verified_at)'), [$fromDate, $toDate]);
    }
    
    $orders = $ordersQuery->get();

    if ($orders->isEmpty()) {
        $currentYear = date('Y');
        $currentQuarter = ceil(date('n') / 3);
        return [
            ['product', $currentYear . ' Q' . $currentQuarter],
            ['Approved', 0],
            ['Pending', 0],
            ['Declined', 0]
        ];
    }

    $periods = $orders->map(function($order) {
        return $order->year . ' Q' . $order->quarter;
    })->unique()->sort()->values()->toArray();

    $statuses = ['Approved', 'Pending', 'Declined'];

    $source = [
        array_merge(['product'], $periods)
    ];

    foreach ($statuses as $status) {
        $row = [$status];
        foreach ($periods as $period) {
            list($year, $quarterPart) = explode(' Q', $period);
            $quarter = (int)$quarterPart;
            
            $orderData = $orders->where('year', $year)
                               ->where('quarter', $quarter)
                               ->where('verification_status', $status)
                               ->first();
            $row[] = $orderData ? (int)$orderData->count : 0;
        }
        $source[] = $row;
    }

    return $source;
}
}