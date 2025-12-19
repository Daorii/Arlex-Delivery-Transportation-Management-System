<?php

namespace App\Http\Controllers;

use App\Models\TransportOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $statusFilter = $request->get('status'); // NEW: Get status filter
    $perPage = 10;

    // Build the query
    $query = Invoice::with(['transportOrder.billing.client'])
        ->where('is_archived', false);

    // ✅ NEW: Apply status filter
    if ($statusFilter === 'pending') {
        // Show invoices that are either "Sent" or "Partially Paid"
        $query->whereIn('invoice_status', ['Sent', 'Partially Paid']);
    }

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('invoice_no', 'like', "%{$search}%")
              ->orWhere('voucher_no', 'like', "%{$search}%")
              ->orWhere('invoice_status', 'like', "%{$search}%")
              ->orWhereHas('transportOrder', function($toQuery) use ($search) {
                  $toQuery->where('to_ref_no', 'like', "%{$search}%");
              })
              ->orWhereHas('transportOrder.billing.client', function($clientQuery) use ($search) {
                  $clientQuery->where('company_name', 'like', "%{$search}%");
              });
        });
    }

    // Get paginated results
    $invoicesPaginated = $query->orderBy('invoice_id', 'desc')->paginate($perPage);

    // Transform the paginated collection
    $invoices = $invoicesPaginated->getCollection()->map(function($invoice) {
        $transportOrder = TransportOrder::where('to_id', $invoice->to_id)->first();
        $clientName = 'N/A';
        
        if ($transportOrder && $transportOrder->billing_id) {
            $billing = DB::table('billings')->where('billing_id', $transportOrder->billing_id)->first();
            if ($billing && $billing->client_id) {
                $client = DB::table('clients')->where('client_id', $billing->client_id)->first();
                $clientName = $client ? $client->company_name : 'N/A';
            }
        }
        
        return (object)[
            'invoice_id' => $invoice->invoice_id,
            'to_id' => $invoice->to_id,
            'invoice_no' => $invoice->invoice_no,
            'transport_order_ref' => $transportOrder->to_ref_no ?? 'N/A',
            'client_name' => $clientName,
            'invoice_date' => $invoice->invoice_date,
            'due_date' => $invoice->due_date,
            'total_sales' => $invoice->total_sales,
            'net_total' => $invoice->net_total,
            'invoice_status' => $invoice->invoice_status,
            'voucher_no' => $invoice->voucher_no
        ];
    });

    // Replace the collection in the paginator
    $invoicesPaginated->setCollection($invoices);

    return view('IP.invoice', [
        'invoices' => $invoicesPaginated,
        'search' => $search,
        'statusFilter' => $statusFilter // ✅ NEW: Pass filter to view
    ]);
}

    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:invoices',
            'to_id' => 'required|integer|exists:transport_orders,to_id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'total_sales' => 'required|numeric|min:0',
            'net_total' => 'required|numeric|min:0',
            'voucher_no' => 'nullable|string',
            'invoice_status' => 'required|in:Draft,Sent,Partially Paid,Fully Paid,Overdue,Cancelled'
        ]);

        $invoiceId = DB::table('invoices')->insertGetId([
            'invoice_no' => $validated['invoice_no'],
            'to_id' => $validated['to_id'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'] ?? null,
            'total_sales' => $validated['total_sales'],
            'net_total' => $validated['net_total'],
            'voucher_no' => $validated['voucher_no'] ?? null,
            'invoice_status' => $validated['invoice_status']
            // Removed created_at and updated_at
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully!',
            'invoice_id' => $invoiceId
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        \Log::error('Error creating invoice', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function getTransportOrders(Request $request)
{
    $query = $request->input('query', '');
    
    // Get all to_ref_no values that already have invoices
    $invoicedToRefNos = DB::table('invoices')
        ->join('transport_orders', 'invoices.to_id', '=', 'transport_orders.to_id')
        ->pluck('transport_orders.to_ref_no')
        ->unique()
        ->toArray();
    
    // Get approved Transport Orders grouped by to_ref_no, excluding those with invoices
    $transportOrders = TransportOrder::select('to_ref_no', 
            DB::raw('MIN(to_id) as to_id'),
            'sipa_ref_no',
            DB::raw('SUM(total_amount) as total_amount')
        )
        ->where('verification_status', 'Approved')
        ->where('to_ref_no', 'LIKE', "%{$query}%")
        ->whereNotIn('to_ref_no', $invoicedToRefNos) // Exclude to_ref_no that have invoices
        ->groupBy('to_ref_no', 'sipa_ref_no')
        ->limit(10)
        ->get()
        ->map(function($to) {
            return [
                'to_id' => $to->to_id,
                'to_ref_no' => $to->to_ref_no,
                'sipa_ref_no' => $to->sipa_ref_no,
                'total_amount' => $to->total_amount
            ];
        });

    return response()->json($transportOrders);
}

    public function view($invoiceId)
    {
        try {
            $invoice = Invoice::with('payments')->findOrFail($invoiceId);
            
            // Get transport order and client name
            $transportOrder = TransportOrder::where('to_id', $invoice->to_id)->first();
            $clientName = 'N/A';
            
            if ($transportOrder && $transportOrder->billing_id) {
                $billing = DB::table('billings')->where('billing_id', $transportOrder->billing_id)->first();
                if ($billing && $billing->client_id) {
                    $client = DB::table('clients')->where('client_id', $billing->client_id)->first();
                    $clientName = $client ? $client->company_name : 'N/A';
                }
            }

            // Get completed payments
            $completedPayments = $invoice->payments()->where('payment_status', 'Completed')->get();
            $totalPaid = $completedPayments->sum('payment_amount');
            $remainingBalance = $invoice->net_total - $totalPaid;

            return response()->json([
                'success' => true,
                'data' => [
                    'invoice' => [
                        'invoice_id' => $invoice->invoice_id,
                        'invoice_no' => $invoice->invoice_no,
                        'to_ref_no' => $transportOrder->to_ref_no ?? 'N/A',
                        'client_name' => $clientName,
                        'invoice_date' => $invoice->invoice_date,
                        'due_date' => $invoice->due_date,
                        'total_sales' => $invoice->total_sales,
                        'net_total' => $invoice->net_total,
                        'invoice_status' => $invoice->invoice_status,
                        'voucher_no' => $invoice->voucher_no
                    ],
                    'payments' => $completedPayments,
                    'total_paid' => $totalPaid,
                    'remaining_balance' => $remainingBalance
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error viewing invoice', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoiceId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $invoiceId)
{
    try {
        $validated = $request->validate([
            'invoice_status' => 'required|in:Draft,Sent,Partially Paid,Fully Paid,Overdue,Cancelled'
        ]);

        DB::table('invoices')
            ->where('invoice_id', $invoiceId)
            ->update([
                'invoice_status' => $validated['invoice_status']
                // Removed updated_at
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice status updated successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating invoice status', [
            'error' => $e->getMessage(),
            'invoice_id' => $invoiceId
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Show archived invoices
public function archived()
{
    $archivedInvoices = Invoice::with(['transportOrder.billing.client'])
        ->where('is_archived', true)
        ->orderBy('invoice_id', 'desc')
        ->get()
        ->map(function($invoice) {
            $transportOrder = TransportOrder::where('to_id', $invoice->to_id)->first();
            $clientName = 'N/A';
            
            if ($transportOrder && $transportOrder->billing_id) {
                $billing = DB::table('billings')->where('billing_id', $transportOrder->billing_id)->first();
                if ($billing && $billing->client_id) {
                    $client = DB::table('clients')->where('client_id', $billing->client_id)->first();
                    $clientName = $client ? $client->company_name : 'N/A';
                }
            }
            
            return [
                'invoice_id' => $invoice->invoice_id,
                'to_id' => $invoice->to_id,
                'invoice_no' => $invoice->invoice_no,
                'transport_order_ref' => $transportOrder->to_ref_no ?? 'N/A',
                'client_name' => $clientName,
                'invoice_date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'total_sales' => $invoice->total_sales,
                'net_total' => $invoice->net_total,
                'invoice_status' => $invoice->invoice_status
            ];
        });

    return view('IP.invoice_archived', compact('archivedInvoices'));
}

// Archive invoice
public function archive($invoiceId)
{
    try {
        DB::table('invoices')
            ->where('invoice_id', $invoiceId)
            ->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice archived successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error archiving invoice', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Restore invoice
public function restore($invoiceId)
{
    try {
        DB::table('invoices')
            ->where('invoice_id', $invoiceId)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice restored successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error restoring invoice', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Permanent delete
public function destroy($invoiceId)
{
    try {
        DB::table('invoices')
            ->where('invoice_id', $invoiceId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice permanently deleted!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error deleting invoice', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
}