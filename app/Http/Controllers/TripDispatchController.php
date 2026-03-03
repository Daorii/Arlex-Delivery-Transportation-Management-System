<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Dispatch;
use App\Models\TripDetail;
use App\Models\SipaDetail;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripDispatchController extends Controller
{
    public function show($clientId)
    {
        // Fetch client info from DB
        $client = Client::where('client_id', $clientId)->first();

        if (!$client) {
            return redirect()->route('TD.TripClient')->with('error', 'Client not found');
        }

        // Get all SIPA IDs for this client
        $sipaIds = DB::table('siparequest')
            ->where('client_id', $clientId)
            ->pluck('sipa_id');

        // Get all non-archived dispatches for these SIPAs
$dispatches = Dispatch::whereIn('sipa_id', $sipaIds)
    ->where('is_archived', false)
    ->get()
            ->map(function($dispatch) {
                // Get driver info
                $driver = Driver::where('driver_id', $dispatch->driver_id)->first();
                $truck = Truck::where('truck_id', $dispatch->truck_id)->first();
                
                // Get all trip details (EIRs) for this dispatch
                $tripDetails = TripDetail::where('dispatch_id', $dispatch->dispatch_id)
                    ->get()
                    ->map(function($trip) {
                        // Get SIPA detail for size, type, price
                        $sipaDetail = SipaDetail::where('sipa_detail_id', $trip->sipa_detail_id)->first();

                        $sipa = $sipaDetail ? $sipaDetail->sipa : null;
                        
                        return [
                            'detail_id' => $trip->detail_id,  // ADD THIS - use the actual primary key
                            'sipa_detail_id' => $trip->sipa_detail_id,
                            'container_no' => $trip->container_no,
                            'eir_no' => $trip->eir_no,
                            'delivery_date' => $trip->delivery_date,
                            'size' => $sipaDetail ? $sipaDetail->size : 'N/A',
                            'type' => $sipa ? $sipa->type : 'N/A',    
                            'price' => $sipaDetail ? $sipaDetail->price : 0,
                            'status' => ($trip->is_verified == 1) ? 'approved' : 'pending'
                        ];
                    });

                return [
                    'id' => $dispatch->dispatch_id,
                    'driver_name' => $driver ? trim($driver->fname . ' ' . ($driver->mname ?? '') . ' ' . $driver->lname) : 'N/A',
                    'vehicle' => $truck ? $truck->plate_no : 'N/A',
                    'submitted_at' => $dispatch->created_at ?? now()->format('Y-m-d H:i'),
                    'status' => $dispatch->status,
                    'total_eirs' => $tripDetails->count(),
                    'eirs' => $tripDetails->toArray()
                ];
            });

        return view('TD.TripDispatch', compact('client', 'dispatches'));
    }

        public function saveReview(Request $request)
{
    \Log::info('saveReview payload:', $request->all());

    $dispatchId = $request->input('dispatch_id');
    $eirs = $request->input('eirs');

    if (!$eirs || !is_array($eirs)) {
        return response()->json(['success' => false, 'message' => 'No EIRs provided']);
    }

    foreach ($eirs as $eir) {
        if (!isset($eir['detail_id'])) {
            \Log::warning('Skipping EIR with missing detail_id', ['eir' => $eir]);
            continue;
        }

        $trip = TripDetail::find($eir['detail_id']);
        if ($trip) {
            // Update approval status
            $trip->is_verified = $eir['status'] === 'approved' ? 1 : 0;
            $trip->verified_at = now();
            
            // Update ALL editable fields
            $trip->container_no = $eir['container_no'];
            $trip->eir_no = $eir['eir_no'];
            $trip->delivery_date = $eir['delivery_date'];
            
            // Update price in sipa_detail if it exists
            if (isset($eir['sipa_detail_id']) && isset($eir['price'])) {
                $sipaDetail = SipaDetail::where('sipa_detail_id', $eir['sipa_detail_id'])->first();
                if ($sipaDetail) {
                    $sipaDetail->price = $eir['price'];
                    $sipaDetail->size = $eir['size'];
                    $sipaDetail->save();
                }
            }
            
            // Update type in sipa if it exists
            if (isset($eir['sipa_detail_id']) && isset($eir['type'])) {
                $sipaDetail = SipaDetail::where('sipa_detail_id', $eir['sipa_detail_id'])->first();
                if ($sipaDetail && $sipaDetail->sipa) {
                    $sipaDetail->sipa->type = $eir['type'];
                    $sipaDetail->sipa->save();
                }
            }
            
            $trip->save();
        } else {
            \Log::warning('TripDetail not found', ['detail_id' => $eir['detail_id']]);
        }
    }

    // ✅ NEW: Auto-update Dispatch status based on trip verification
    $this->updateDispatchStatus($dispatchId);

    return response()->json(['success' => true, 'message' => 'Review saved successfully']);
}

/**
 * ✅ NEW METHOD: Auto-update dispatch status based on trip details
 */
private function updateDispatchStatus($dispatchId)
{
    $dispatch = Dispatch::find($dispatchId);
    
    if (!$dispatch) {
        return;
    }

    // Get all trip details for this dispatch
    $allTrips = TripDetail::where('dispatch_id', $dispatchId)->get();
    
    // If no trips exist, keep status as is
    if ($allTrips->isEmpty()) {
        return;
    }

    $totalTrips = $allTrips->count();
    $verifiedTrips = $allTrips->where('is_verified', 1)->count();
    $pendingTrips = $allTrips->where('is_verified', 0)->count();

    // Determine new status
    if ($verifiedTrips === $totalTrips && $totalTrips > 0) {
        // All trips are verified → Completed
        $newStatus = 'completed';
    } elseif ($verifiedTrips > 0 && $pendingTrips > 0) {
        // Some verified, some pending → In Progress
        $newStatus = 'in_progress';
    } else {
        // All pending or no trips → Pending
        $newStatus = 'pending';
    }

    // Update dispatch status if it changed
    if ($dispatch->status !== $newStatus) {
        $dispatch->status = $newStatus;
        $dispatch->save();
        
        \Log::info("Dispatch #{$dispatchId} status updated to: {$newStatus}", [
            'total_trips' => $totalTrips,
            'verified' => $verifiedTrips,
            'pending' => $pendingTrips
        ]);
    }
}

    // Archive a dispatch
public function archive($id)
{
    $dispatch = Dispatch::findOrFail($id);
    $dispatch->is_archived = true;
    $dispatch->save();

    return response()->json(['success' => true, 'message' => 'Dispatch archived successfully']);
}

// Restore a dispatch
public function restore($id)
{
    $dispatch = Dispatch::findOrFail($id);
    $dispatch->is_archived = false;
    $dispatch->save();

    return response()->json(['success' => true, 'message' => 'Dispatch restored successfully']);
}

// Permanently delete a dispatch
public function destroy($id)
{
    try {
        $dispatch = Dispatch::findOrFail($id);
        
        // Optional: Delete associated trip details first
        TripDetail::where('dispatch_id', $dispatch->dispatch_id)->delete();
        
        // Delete the dispatch
        $dispatch->delete();
        
        return response()->json(['success' => true, 'message' => 'Dispatch deleted successfully']);
    } catch (\Exception $e) {
        \Log::error('Error deleting dispatch: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error deleting dispatch: ' . $e->getMessage()], 500);
    }
}

// Get archived dispatches
public function archived($clientId)
{
    $client = Client::where('client_id', $clientId)->first();

    if (!$client) {
        return redirect()->route('TD.TripClient')->with('error', 'Client not found');
    }

    $sipaIds = DB::table('siparequest')
        ->where('client_id', $clientId)
        ->pluck('sipa_id');

    // Get ARCHIVED dispatches
    $dispatches = Dispatch::whereIn('sipa_id', $sipaIds)
        ->where('is_archived', true)
        ->get()
        ->map(function($dispatch) {
            $driver = Driver::where('driver_id', $dispatch->driver_id)->first();
            $truck = Truck::where('truck_id', $dispatch->truck_id)->first();
            
            $tripDetails = TripDetail::where('dispatch_id', $dispatch->dispatch_id)
                ->get()
                ->map(function($trip) {
                    $sipaDetail = SipaDetail::where('sipa_detail_id', $trip->sipa_detail_id)->first();
                    $sipa = $sipaDetail ? $sipaDetail->sipa : null;
                    
                    return [
                        'detail_id' => $trip->detail_id,
                        'sipa_detail_id' => $trip->sipa_detail_id,
                        'container_no' => $trip->container_no,
                        'eir_no' => $trip->eir_no,
                        'delivery_date' => $trip->delivery_date,
                        'size' => $sipaDetail ? $sipaDetail->size : 'N/A',
                        'type' => $sipa ? $sipa->type : 'N/A',
                        'price' => $sipaDetail ? $sipaDetail->price : 0,
                        'status' => ($trip->is_verified == 1) ? 'approved' : 'pending'
                    ];
                });

            return [
                'id' => $dispatch->dispatch_id,
                'driver_name' => $driver ? trim($driver->fname . ' ' . ($driver->mname ?? '') . ' ' . $driver->lname) : 'N/A',
                'vehicle' => $truck ? $truck->plate_no : 'N/A',
                'submitted_at' => $dispatch->created_at ?? now()->format('Y-m-d H:i'),
                'status' => $dispatch->status,
                'total_eirs' => $tripDetails->count(),
                'eirs' => $tripDetails->toArray()
            ];
        });

    return view('TD.TripDispatchArchived', compact('client', 'dispatches'));
}

}

