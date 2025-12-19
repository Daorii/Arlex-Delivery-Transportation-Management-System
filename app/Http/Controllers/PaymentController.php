<?php

namespace App\Http\Controllers;

use App\Models\TransportOrder;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $perPage = 10;

    // Build the query
    $query = Payment::with('invoice')
        ->where('is_archived', false);

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('payment_ref_no', 'like', "%{$search}%")
              ->orWhere('payment_date', 'like', "%{$search}%")
              ->orWhere('payment_method', 'like', "%{$search}%")
              ->orWhere('payment_status', 'like', "%{$search}%")
              ->orWhere('received_by', 'like', "%{$search}%")
              ->orWhereHas('invoice', function($invoiceQuery) use ($search) {
                  $invoiceQuery->where('invoice_no', 'like', "%{$search}%");
              });
        });
    }

    // Get paginated results
    $paymentsQuery = $query->orderBy('payment_id', 'desc')->paginate($perPage);

    // Transform the paginated collection
    $payments = $paymentsQuery->getCollection()->map(function($payment) {
        $to_ref_no = 'N/A';
        if ($payment->invoice && $payment->invoice->to_id) {
            $transportOrder = TransportOrder::where('to_id', $payment->invoice->to_id)->first();
            $to_ref_no = $transportOrder->to_ref_no ?? 'N/A';
        }
        
        return (object)[
            'payment_id' => $payment->payment_id,
            'invoice_id' => $payment->invoice_id,
            'invoice_no' => $payment->invoice->invoice_no ?? 'N/A',
            'to_ref_no' => $to_ref_no,
            'payment_ref_no' => $payment->payment_ref_no,
            'payment_date' => $payment->payment_date,
            'payment_amount' => $payment->payment_amount,
            'payment_method' => $payment->payment_method,
            'bank_name' => $payment->bank_name,
            'check_number' => $payment->check_number,
            'transaction_ref_no' => $payment->transaction_ref_no,
            'payment_status' => $payment->payment_status,
            'remarks' => $payment->remarks,
            'received_by' => $payment->received_by
        ];
    });

    // Replace the collection in the paginator
    $paymentsQuery->setCollection($payments);

    return view('IP.payment', compact('paymentsQuery', 'search'));
}

    public function searchInvoices(Request $request)
{
    $query = $request->input('query');
    
    // Search for invoices with remaining balance > 0
    $invoices = Invoice::with(['transportOrder', 'payments'])
        ->where(function($q) use ($query) {
            $q->where('invoice_no', 'LIKE', "%{$query}%");
        })
        ->orWhereHas('transportOrder', function($tq) use ($query) {
            $tq->where('to_ref_no', 'LIKE', "%{$query}%");
        })
        ->whereIn('invoice_status', ['Sent', 'Partially Paid', 'Fully Paid'])
        ->limit(50) // Get more results first
        ->get()
        ->filter(function($invoice) {
            // Filter invoices that have remaining balance > 0
            return $invoice->remaining_balance > 0;
        })
        ->take(10) // Then limit to 10
        ->map(function($invoice) {
            // Get the first transport order for this invoice
            $transportOrder = TransportOrder::where('to_id', $invoice->to_id)->first();
            
            return [
                'invoice_id' => $invoice->invoice_id,
                'invoice_no' => $invoice->invoice_no,
                'to_ref_no' => $transportOrder->to_ref_no ?? 'N/A',
                'net_total' => $invoice->net_total,
                'paid_amount' => $invoice->total_paid,
                'remaining_balance' => $invoice->remaining_balance,
                'invoice_status' => $invoice->invoice_status
            ];
        })
        ->values(); // Reset array keys

    return response()->json($invoices);
}

    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_id',
            'payment_ref_no' => 'required|string|unique:payments,payment_ref_no',
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Cash,Check,Bank Transfer,Online Payment',
            'bank_name' => 'nullable|string',
            'check_number' => 'nullable|string',
            'transaction_ref_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'received_by' => 'nullable|string'
        ]);

        // Get the invoice with relationships
        $invoice = Invoice::with('payments')->findOrFail($validated['invoice_id']);
        
        // Calculate remaining balance using accessor
        $remainingBalance = $invoice->remaining_balance;

        // Check if payment amount doesn't exceed remaining balance
        if ($validated['payment_amount'] > $remainingBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount (₱' . number_format($validated['payment_amount'], 2) . ') exceeds remaining balance (₱' . number_format($remainingBalance, 2) . ')'
            ], 400);
        }

        // Auto-set status to Completed
        $validated['payment_status'] = 'Completed';

        // Create the payment
        $payment = Payment::create($validated);

        // Update invoice status
        $invoice->updateInvoiceStatus();

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully! Invoice updated to ' . $invoice->fresh()->invoice_status
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

   // Show archived payments
public function archived()
{
    $archivedPayments = Payment::with('invoice')
        ->where('is_archived', true)
        ->orderBy('payment_id', 'desc')
        ->get()
        ->map(function($payment) {
            $to_ref_no = 'N/A';
            if ($payment->invoice && $payment->invoice->to_id) {
                $transportOrder = TransportOrder::where('to_id', $payment->invoice->to_id)->first();
                $to_ref_no = $transportOrder->to_ref_no ?? 'N/A';
            }
            
            return [
                'payment_id' => $payment->payment_id,
                'invoice_id' => $payment->invoice_id,
                'invoice_no' => $payment->invoice->invoice_no ?? 'N/A',
                'to_ref_no' => $to_ref_no,
                'payment_ref_no' => $payment->payment_ref_no,
                'payment_date' => $payment->payment_date,
                'payment_amount' => $payment->payment_amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status
            ];
        });

    return view('IP.payment_archived', compact('archivedPayments'));
}

// Archive payment
public function archive($paymentId)
{
    try {
        DB::table('payments')
            ->where('payment_id', $paymentId)
            ->update(['is_archived' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Payment archived successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error archiving payment', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Restore payment
public function restore($paymentId)
{
    try {
        DB::table('payments')
            ->where('payment_id', $paymentId)
            ->update(['is_archived' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Payment restored successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error restoring payment', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// UPDATE the destroy method to be permanent delete
public function destroy($id)
{
    try {
        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;

        // Delete payment permanently
        $payment->delete();

        // Update invoice status if invoice still exists
        if ($invoice) {
            $invoice->updateInvoiceStatus();
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment permanently deleted!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}