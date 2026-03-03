<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Dispatch;
use App\Models\TripDetail;
use App\Models\SipaDetail;
use App\Models\Billing;   
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BillingController extends Controller
{
    // Show the Billing page
    public function index()
    {
        // Fetch all clients with their details
        $clients = Client::all()->map(function($client) {
            return [
                'id' => $client->client_id,
                'name' => $client->company_name ?? 'N/A',
                'sipa_id' => '',
                'trip_ids' => [],
                'trip_count' => 0,
                'week_period' => '',
                'prepared_by' => '',
                'checked_by' => '',
                'status' => 'pending',
                'created_at' => now()->format('Y-m-d')
            ];
        });
        
        $totalClients = $clients->count();
        

        return view('billing.index', compact('clients', 'totalClients'));
    }

    // Fetch SIPA details and related trips
    public function fetchSipaDetails(Request $request)
{
    try {
        $sipaRefNo = $request->input('sipa_ref_no');
        $clientId = $request->input('client_id');

        \Log::info('Fetching SIPA details', ['sipa_ref_no' => $sipaRefNo, 'client_id' => $clientId]);

        // Find SIPA by reference number and verify it belongs to this client
        $sipa = DB::table('siparequest')
            ->where('sipa_ref_no', $sipaRefNo)
            ->where('client_id', $clientId)
            ->first();

        \Log::info('SIPA query result', ['sipa' => $sipa]);

        if (!$sipa) {
            return response()->json([
                'success' => false,
                'message' => 'SIPA Reference Number "' . $sipaRefNo . '" not found for this client'
            ]);
        }

        $sipaId = $sipa->sipa_id;

        // Get all dispatches for this SIPA
        $dispatches = Dispatch::where('sipa_id', $sipaId)->get();

        \Log::info('Dispatches found', ['count' => $dispatches->count()]);

        if ($dispatches->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No dispatches found for this SIPA'
            ]);
        }

        // Get all trip details for these dispatches
        $dispatchIds = $dispatches->pluck('dispatch_id');
        $tripDetails = TripDetail::whereIn('dispatch_id', $dispatchIds)
            ->where('is_verified', 1) // Only verified trips
            ->orderBy('delivery_date', 'asc')
            ->get();

        \Log::info('Trip details found', ['count' => $tripDetails->count()]);

        if ($tripDetails->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No verified trip details found for this SIPA. Please ensure trips are verified first.'
            ]);
        }

        // Get trip IDs (container numbers or EIR numbers)
        $tripIds = $tripDetails->map(function($trip) {
            return $trip->container_no . ' (' . $trip->eir_no . ')';
        })->toArray();

        // Calculate week period
        $firstDeliveryDate = Carbon::parse($tripDetails->first()->delivery_date);
        $lastDeliveryDate = Carbon::parse($tripDetails->last()->delivery_date);
        
        // Calculate week numbers
        $startWeek = $firstDeliveryDate->weekOfYear;
        $endWeek = $lastDeliveryDate->weekOfYear;
        
        // Format week period
        if ($startWeek === $endWeek) {
            $weekPeriod = "Week " . $startWeek;
        } else {
            $weekPeriod = "Week " . $startWeek . " - " . $endWeek;
        }

        // Calculate total weeks
        $totalWeeks = $firstDeliveryDate->diffInWeeks($lastDeliveryDate) + 1;

        $responseData = [
            'success' => true,
            'data' => [
                'sipa_id' => $sipaId, // Return the actual SIPA ID for backend use
                'trip_ids' => $tripIds,
                'trip_count' => $tripDetails->count(),
                'week_period' => $weekPeriod,
                'total_weeks' => $totalWeeks,
                'start_date' => $firstDeliveryDate->format('Y-m-d'),
                'end_date' => $lastDeliveryDate->format('Y-m-d'),
                'sipa_ref_no' => $sipa->sipa_ref_no
            ]
        ];

        \Log::info('Response data', $responseData);

        return response()->json($responseData);

    } catch (\Exception $e) {
        \Log::error('Error fetching SIPA details', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    // Handle SoA generation
    public function generate(Request $request)
{
    try {
        $request->validate([
            'id' => 'required',
            'sipa_id' => 'required',
            'prepared_by' => 'required',
            'checked_by' => 'required',
        ]);

        $clientId = $request->input('id');
        $sipaId = $request->input('sipa_id');

        // Get client details
        $client = Client::find($clientId);
        
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

        // Build SOA items with pricing from sipadetails
        $soaItems = [];
        $totalAmount = 0;

        foreach ($tripDetails as $trip) {
            $sipaDetail = SipaDetail::where('sipa_detail_id', $trip->sipa_detail_id)->first();
            
            $amount = $sipaDetail ? (float)$sipaDetail->price : 0;
            $totalAmount += $amount;

            $soaItems[] = [
                'delivery_date' => Carbon::parse($trip->delivery_date)->format('n/j/Y'),
                'container_no' => $trip->container_no,
                'eir_no' => $trip->eir_no,
                'size' => $sipaDetail ? $sipaDetail->size : 'N/A',
                'destination' => $sipaDetail ? ($sipaDetail->route_to ?? 'N/A') : 'N/A',
                'amount' => number_format($amount, 2),
                'remarks' => 'MT'
            ];
        }

        // Generate SOA number (format: YY-## based on date)
        $soaNumber = date('y') . '-' . date('m');

        return response()->json([
            'success' => true,
            'message' => 'Statement of Account generated successfully!',
            'data' => [
                'soa_number' => $soaNumber,
                'date' => now()->format('n/j/Y'),
                'client_name' => $client->company_name ?? 'N/A',
                'client_address' => $client->address ?? 'N/A',
                'sipa_ref_no' => $sipa->sipa_ref_no ?? 'N/A',
                'items' => $soaItems,
                'total_amount' => number_format($totalAmount, 2),
                'prepared_by' => $request->input('prepared_by'),
                'checked_by' => $request->input('checked_by'),
                'week_period' => $request->input('week_period')
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error generating SOA', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error generating SOA: ' . $e->getMessage()
        ], 500);
    }
}

    public function save(Request $request)
{
    try {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'sipa_id' => 'required',
            'sipa_ref_no' => 'required|string',
            'week_period' => 'required|string',
            'prepared_by' => 'required|string',
            'checked_by' => 'required|string',
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $billingId = DB::table('billings')->insertGetId([
            'client_id' => $request->client_id,
            'sipa_id' => $request->sipa_id,
            'sipa_ref_no' => $request->sipa_ref_no,
            'week_period_text' => $request->week_period,
            'prepared_by' => $request->prepared_by,
            'checked_by' => $request->checked_by,
            'total_amount' => $request->total_amount,
            'status' => $request->status,
            'created_at' => now(),
            // removed updated_at - it doesn't exist in your table
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOA saved successfully!',
            'billing_id' => $billingId
        ]);
    } catch (\Exception $e) {
        \Log::error('Error saving SOA', ['error' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'Error saving SOA: ' . $e->getMessage()
        ], 500);
    }
}
    

        public function records(Request $request)
{
    $search = $request->get('search');
    $perPage = 10;

    // Build the query
    $query = Billing::with('client')
        ->where('is_archived', false);

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('billing_id', 'like', "%{$search}%")
              ->orWhere('week_period_text', 'like', "%{$search}%")
              ->orWhere('prepared_by', 'like', "%{$search}%")
              ->orWhere('checked_by', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhereHas('client', function($clientQuery) use ($search) {
                  $clientQuery->where('company_name', 'like', "%{$search}%");
              });
        });
    }

    // Get paginated results
    $billings = $query->orderBy('created_at', 'desc')->paginate($perPage);

    // Transform the paginated collection
    $billingRecords = $billings->getCollection()->map(function ($billing) {
        return (object)[
            'billing_id' => $billing->billing_id,
            'client_name' => $billing->client->company_name ?? 'N/A',
            'week_period_text' => $billing->week_period_text,
            'prepared_by' => $billing->prepared_by,
            'checked_by' => $billing->checked_by,
            'total_amount' => $billing->total_amount,
            'status' => ucfirst($billing->status),
        ];
    });

    // Replace the collection in the paginator
    $billings->setCollection($billingRecords);

    return view('billing.BillingRecord', [
        'billingRecords' => $billings,
        'search' => $search
    ]);
}

    // View full billing details with SOA items
public function view($billingId)
{
    try {
        $billing = Billing::with('client')->where('billing_id', $billingId)->first();
        
        if (!$billing) {
            return response()->json([
                'success' => false,
                'message' => 'Billing not found'
            ], 404);
        }

        $sipaId = DB::table('billings')->where('billing_id', $billingId)->value('sipa_id');
        
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

        // Build SOA items with pricing
        $soaItems = [];
        foreach ($tripDetails as $trip) {
            $sipaDetail = SipaDetail::where('sipa_detail_id', $trip->sipa_detail_id)->first();
            
            $amount = $sipaDetail ? (float)$sipaDetail->price : 0;

            $soaItems[] = [
                'delivery_date' => Carbon::parse($trip->delivery_date)->format('n/j/Y'),
                'container_no' => $trip->container_no,
                'eir_no' => $trip->eir_no,
                'size' => $sipaDetail ? $sipaDetail->size : 'N/A',
                'destination' => $sipaDetail ? ($sipaDetail->route_to ?? 'N/A') : 'N/A',
                'amount' => number_format($amount, 2),
                'remarks' => 'MT'
            ];
        }

        // Generate SOA number
        $soaNumber = date('y') . '-' . date('m');

        return response()->json([
            'success' => true,
            'data' => [
                'billing_id' => $billing->billing_id,
                'soa_number' => $soaNumber,
                'date' => Carbon::parse($billing->created_at)->format('n/j/Y'),
                'client_name' => $billing->client->company_name ?? 'N/A',
                'client_address' => $billing->client->address ?? 'N/A',
                'sipa_ref_no' => $sipa->sipa_ref_no ?? 'N/A',
                'week_period' => $billing->week_period_text,
                'prepared_by' => $billing->prepared_by,
                'checked_by' => $billing->checked_by,
                'total_amount' => number_format($billing->total_amount, 2),
                'status' => $billing->status,
                'items' => $soaItems
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error viewing billing', [
            'error' => $e->getMessage(),
            'billing_id' => $billingId
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Update billing status only
public function updateStatus(Request $request, $billingId)
{
    try {
        $request->validate([
            'status' => 'required|string|in:draft,pending,approved,paid,cancelled'
        ]);

        DB::table('billings')
            ->where('billing_id', $billingId)
            ->update([
                'status' => $request->status
                // removed updated_at - it doesn't exist in your table
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating billing status', [
            'error' => $e->getMessage(),
            'billing_id' => $billingId
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Get all SIPA reference numbers for a specific client
public function getClientSipas($clientId)
{
    try {
        // Get SIPA reference numbers that have APPROVED Transport Orders ONLY
        $usedSipaRefNos = DB::table('transport_orders')
            ->where('verification_status', 'Approved')  // ← ONLY Approved TOs
            ->distinct()
            ->pluck('sipa_ref_no')
            ->toArray();

        // Get SIPAs for this client that DON'T have approved TOs
        $sipas = DB::table('siparequest')
            ->where('client_id', $clientId)
            ->whereNotIn('sipa_ref_no', $usedSipaRefNos)
            ->select('sipa_id', 'sipa_ref_no', 'type')
            ->orderBy('sipa_ref_no', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'sipas' => $sipas
        ]);

    } catch (\Exception $e) {
        \Log::error('Error fetching client SIPAs', [
            'error' => $e->getMessage(),
            'client_id' => $clientId
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error fetching SIPA records'
        ], 500);
    }
}

// Show archived billings
public function archived()
{
    $archivedBillings = Billing::with('client')
        ->where('is_archived', true)
        ->get()
        ->map(function ($billing) {
            $soaArchiveUrl = null;
            try {
                $soaArchiveUrl = $this->getArchivedSoaUrl((int) $billing->billing_id);
            } catch (\Throwable $e) {
                \Log::warning('Unable to resolve archived SOA URL', [
                    'billing_id' => $billing->billing_id,
                    'error' => $e->getMessage(),
                ]);
            }

            return [
                'billing_id' => $billing->billing_id,
                'client_name' => $billing->client->company_name ?? 'N/A',
                'week_period_text' => $billing->week_period_text,
                'prepared_by' => $billing->prepared_by,
                'checked_by' => $billing->checked_by,
                'total_amount' => $billing->total_amount,
                'status' => ucfirst($billing->status),
                'soa_archive_url' => $soaArchiveUrl,
            ];
        });

    return view('billing.BillingArchived', [
        'archivedBillings' => $archivedBillings
    ]);
}

// Archive billing
public function archive($id)
{
    try {
        $billing = Billing::findOrFail($id);

        $archiveUploadWarning = null;

        // Best-effort S3 upload; do not block archive action if S3 is temporarily unavailable.
        try {
            $this->uploadArchivedSoaCopy($billing);
        } catch (\Throwable $uploadError) {
            $archiveUploadWarning = 'Archived in database, but SOA copy upload failed.';
            \Log::warning('SOA archive upload failed', [
                'billing_id' => $id,
                'error' => $uploadError->getMessage(),
            ]);
        }

        $billing->is_archived = true;
        $billing->save();

        return response()->json([
            'success' => true,
            'message' => $archiveUploadWarning ?: 'Billing archived successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error archiving billing', [
            'error' => $e->getMessage(),
            'billing_id' => $id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error archiving billing: ' . $e->getMessage()
        ], 500);
    }
}

private function buildSoaData(Billing $billing): array
{
    $sipa = DB::table('siparequest')->where('sipa_id', $billing->sipa_id)->first();

    $dispatchIds = Dispatch::where('sipa_id', $billing->sipa_id)
        ->pluck('dispatch_id');

    $tripDetails = TripDetail::whereIn('dispatch_id', $dispatchIds)
        ->where('is_verified', 1)
        ->orderBy('delivery_date', 'asc')
        ->get();

    $soaItems = [];

    foreach ($tripDetails as $trip) {
        $sipaDetail = SipaDetail::where('sipa_detail_id', $trip->sipa_detail_id)->first();
        $amount = $sipaDetail ? (float) $sipaDetail->price : 0;

        $soaItems[] = [
            'delivery_date' => Carbon::parse($trip->delivery_date)->format('n/j/Y'),
            'container_no' => $trip->container_no,
            'eir_no' => $trip->eir_no,
            'size' => $sipaDetail ? $sipaDetail->size : 'N/A',
            'destination' => $sipaDetail ? ($sipaDetail->route_to ?? 'N/A') : 'N/A',
            'amount' => number_format($amount, 2),
            'remarks' => 'MT',
        ];
    }

    return [
        'billing_id' => $billing->billing_id,
        'soa_number' => date('y') . '-' . date('m'),
        'date' => Carbon::parse($billing->created_at)->format('n/j/Y'),
        'client_name' => $billing->client->company_name ?? 'N/A',
        'client_address' => $billing->client->address ?? 'N/A',
        'sipa_ref_no' => $sipa->sipa_ref_no ?? 'N/A',
        'week_period' => $billing->week_period_text,
        'prepared_by' => $billing->prepared_by,
        'checked_by' => $billing->checked_by,
        'total_amount' => number_format((float) $billing->total_amount, 2),
        'status' => $billing->status,
        'items' => $soaItems,
    ];
}

private function uploadArchivedSoaCopy(Billing $billing): void
{
    $soaData = $this->buildSoaData($billing->loadMissing('client'));
    $html = view('billing.soa_archive', ['soa' => $soaData])->render();

    // Keep deterministic key so archived list can always find latest copy.
    $baseKey = 'soa-archives/billing-' . $billing->billing_id;
    $pdfKey = $baseKey . '.pdf';
    $htmlKey = $baseKey . '.html';

    // Prefer PDF when Dompdf is available; fallback to HTML snapshot.
    if (class_exists(\Dompdf\Dompdf::class)) {
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        Storage::disk('s3')->put($pdfKey, $dompdf->output(), [
            'ContentType' => 'application/pdf',
        ]);
        return;
    }

    Storage::disk('s3')->put($htmlKey, $html, [
        'ContentType' => 'text/html; charset=UTF-8',
    ]);
}

private function getArchivedSoaUrl(int $billingId): ?string
{
    try {
        $pdfKey = 'soa-archives/billing-' . $billingId . '.pdf';
        $htmlKey = 'soa-archives/billing-' . $billingId . '.html';
        $disk = Storage::disk('s3');

        $key = null;
        if ($disk->exists($pdfKey)) {
            $key = $pdfKey;
        } elseif ($disk->exists($htmlKey)) {
            $key = $htmlKey;
        }

        if (! $key) {
            return null;
        }

        return $disk->temporaryUrl($key, now()->addHours(4));
    } catch (\Throwable $e) {
        // Fallback for drivers that do not support temporary URLs, or return null if storage is unreachable.
        try {
            $disk = Storage::disk('s3');
            $pdfKey = 'soa-archives/billing-' . $billingId . '.pdf';
            $htmlKey = 'soa-archives/billing-' . $billingId . '.html';

            if ($disk->exists($pdfKey)) {
                return $disk->url($pdfKey);
            }
            if ($disk->exists($htmlKey)) {
                return $disk->url($htmlKey);
            }
        } catch (\Throwable $inner) {
            \Log::warning('S3 lookup failed while resolving archived SOA URL', [
                'billing_id' => $billingId,
                'error' => $inner->getMessage(),
            ]);
        }

        return null;
    }
}

// Restore billing
public function restore($id)
{
    try {
        $billing = Billing::findOrFail($id);
        $billing->is_archived = false;
        $billing->save();

        return response()->json([
            'success' => true,
            'message' => 'Billing restored successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error restoring billing', [
            'error' => $e->getMessage(),
            'billing_id' => $id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error restoring billing: ' . $e->getMessage()
        ], 500);
    }
}

// Permanent delete
public function destroyBilling($id)
{
    try {
        $billing = Billing::findOrFail($id);
        $billing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Billing permanently deleted!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error deleting billing', [
            'error' => $e->getMessage(),
            'billing_id' => $id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error deleting billing: ' . $e->getMessage()
        ], 500);
    }
}

}

