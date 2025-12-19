<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dispatch;
use App\Models\TripDetail;
use App\Models\Sipadetail;

class TransportOrderController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $perPage = 10;

    // Build the query
    $query = DB::table('transport_orders')
        ->where('is_archived', false);

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('to_ref_no', 'like', "%{$search}%")
              ->orWhere('sipa_ref_no', 'like', "%{$search}%")
              ->orWhere('depot_from', 'like', "%{$search}%")
              ->orWhere('depot_to', 'like', "%{$search}%")
              ->orWhere('verification_status', 'like', "%{$search}%")
              ->orWhere('size', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%");
        });
    }

    // Get paginated results
    $transportOrders = $query->orderBy('created_at', 'desc')->paginate($perPage);

    return view('TO.transport_orders', compact('transportOrders', 'search'));
}

// Show archived transport orders
public function archived()
{
    $archivedOrders = DB::table('transport_orders')
        ->where('is_archived', true)
        ->orderBy('created_at', 'desc')  // ← ADD THIS LINE
        ->get();

    // Group by to_ref_no
    $groupedOrders = [];
    foreach ($archivedOrders as $order) {
        if (!isset($groupedOrders[$order->to_ref_no])) {
            $groupedOrders[$order->to_ref_no] = [
                'to_ref_no' => $order->to_ref_no,
                'sipa_ref_no' => $order->sipa_ref_no,
                'billing_id' => $order->billing_id,
                'verification_status' => $order->verification_status,
                'depot_from' => $order->depot_from,
                'depot_to' => $order->depot_to,
                'items' => [],
                'total_amount' => 0
            ];
        }
        
        $groupedOrders[$order->to_ref_no]['items'][] = [
            'size' => $order->size,
            'type' => $order->type,
            'quantity' => $order->quantity,
            'amount' => $order->total_amount
        ];
        
        $groupedOrders[$order->to_ref_no]['total_amount'] += $order->total_amount;
    }

    return view('TO.transport_orders_archived', [
        'archivedOrders' => array_values($groupedOrders)
    ]);
}

// Archive transport order
public function archive($toRefNo)
{
    try {
        DB::table('transport_orders')
            ->where('to_ref_no', $toRefNo)
            ->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Transport Order archived successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error archiving TO', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Restore transport order
public function restore($toRefNo)
{
    try {
        DB::table('transport_orders')
            ->where('to_ref_no', $toRefNo)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Transport Order restored successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error restoring TO', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Permanent delete
public function destroy($toRefNo)
{
    try {
        DB::table('transport_orders')
            ->where('to_ref_no', $toRefNo)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transport Order permanently deleted!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error deleting TO', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Autocomplete SOA billing IDs (only approved ones that don't have approved TOs)
public function searchSoa(Request $request)
{
    $query = $request->input('query');
    
    // Get billing IDs that have APPROVED Transport Orders
    $usedBillingIds = DB::table('transport_orders')
        ->where('verification_status', 'Approved')  // ← ONLY Approved TOs
        ->distinct()
        ->pluck('billing_id')
        ->toArray();
    
    // Search for APPROVED billings that haven't been used in approved TOs yet
    $soas = DB::table('billings')
        ->join('clients', 'billings.client_id', '=', 'clients.client_id')
        ->where('billings.status', 'approved')
        ->whereNotIn('billings.billing_id', $usedBillingIds)
        ->where(function($q) use ($query) {
            $q->where('billings.billing_id', 'LIKE', "%{$query}%")
              ->orWhere('clients.company_name', 'LIKE', "%{$query}%")
              ->orWhere('billings.sipa_ref_no', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get([
            'billings.billing_id', 
            'clients.company_name',
            'billings.week_period_text',
            'billings.sipa_ref_no'
        ]);

    return response()->json($soas);
}

    // Get SOA details when an SOA is selected
    public function getSoaDetails($billingId)
    {
        try {
            // Get the billing record
            $billing = DB::table('billings')->where('billing_id', $billingId)->first();

            if (!$billing) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Billing not found'
                ], 404);
            }

            if ($billing->status !== 'approved') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Only approved SOAs can be used for Transport Orders'
                ], 400);
            }

            $sipaId = $billing->sipa_id;
            
            if (!$sipaId) {
                return response()->json([
                    'success' => false, 
                    'message' => 'SIPA ID not found for this billing'
                ], 404);
            }

            // Get SIPA details
            $sipa = DB::table('siparequest')->where('sipa_id', $sipaId)->first();

            // Get all dispatches for this SIPA
            $dispatches = Dispatch::where('sipa_id', $sipaId)->get();
            $dispatchIds = $dispatches->pluck('dispatch_id');

            // Get all verified trip details
            $tripDetails = TripDetail::whereIn('dispatch_id', $dispatchIds)
                ->where('is_verified', 1)
                ->orderBy('delivery_date', 'asc')
                ->get();

            // Group trips by size and type to get totals
            $groupedTrips = [];
            
            foreach ($tripDetails as $trip) {
                $sipaDetail = Sipadetail::where('sipa_detail_id', $trip->sipa_detail_id)->first();
                
                if (!$sipaDetail) continue;

                $key = $sipaDetail->size . '_' . $sipa->type; // e.g., "20_Dry" or "40_Reefer"
                
                if (!isset($groupedTrips[$key])) {
                    $groupedTrips[$key] = [
                        'size' => $sipaDetail->size,
                        'type' => $sipa->type,
                        'quantity' => 0,
                        'price_per_unit' => (float)$sipaDetail->price,
                        'depot_from' => $sipaDetail->route_from,
                        'depot_to' => $sipaDetail->route_to ?? 'N/A',
                    ];
                }
                
                $groupedTrips[$key]['quantity']++;
            }

            // Convert to array and calculate totals
            $details = array_values($groupedTrips);

            return response()->json([
                'success' => true,
                'data' => [
                    'billing_id' => $billing->billing_id,
                    'sipa_ref_no' => $billing->sipa_ref_no,
                    'client_name' => DB::table('clients')->where('client_id', $billing->client_id)->value('company_name'),
                    'details' => $details
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching SOA details', [
                'error' => $e->getMessage(),
                'billing_id' => $billingId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'to_ref_no' => 'required|string|unique:transport_orders',
                'billing_id' => 'required|integer|exists:billings,billing_id',
                'sipa_ref_no' => 'required|string',
                'items' => 'required|array|min:1', // Array of items with size/type/quantity
                'items.*.size' => 'required|string',
                'items.*.type' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price_per_unit' => 'required|numeric',
                'items.*.depot_from' => 'required|string',
                'items.*.depot_to' => 'required|string',
                'total_amount' => 'required|numeric'
            ]);

            // Insert each item as a separate transport order
foreach ($validated['items'] as $item) {
    DB::table('transport_orders')->insert([
        'to_ref_no' => $validated['to_ref_no'],
        'billing_id' => $validated['billing_id'],
        'sipa_ref_no' => $validated['sipa_ref_no'],
        'size' => $item['size'],
        'quantity' => $item['quantity'],
        'type' => $item['type'],
        'total_amount' => $item['quantity'] * $item['price_per_unit'],
        'depot_from' => $item['depot_from'],
        'depot_to' => $item['depot_to'],
        'verification_status' => 'Pending',
        'verified_by' => null,
        'verified_at' => null,
        'created_at' => now(),  // ← ADD THIS
        'updated_at' => now()   // ← ADD THIS
    ]);
}

            return response()->json([
                'success' => true,
                'message' => 'Transport Order created successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error creating transport order', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request)
{
    try {
        $request->validate([
            'to_ref_no' => 'required|string',
            'verification_status' => 'required|in:Pending,Approved,Declined'
        ]);

        DB::table('transport_orders')
            ->where('to_ref_no', $request->to_ref_no)
            ->update([
                'verification_status' => $request->verification_status
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating TO status', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}