<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SipaDetail;
use App\Models\SipaRequest;

class SIPARequestController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $perPage = 10;

    // Build the query
    $query = DB::table('clients')
        ->select(
            'clients.client_id',
            DB::raw("CONCAT(clients.fname, ' ', clients.mname, ' ', clients.lname) as client_name"),
            'clients.address',
            'clients.contact as contact_person'
        );

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('clients.fname', 'like', "%{$search}%")
              ->orWhere('clients.mname', 'like', "%{$search}%")
              ->orWhere('clients.lname', 'like', "%{$search}%")
              ->orWhere('clients.address', 'like', "%{$search}%")
              ->orWhere('clients.contact', 'like', "%{$search}%")
              ->orWhere(DB::raw("CONCAT(clients.fname, ' ', clients.mname, ' ', clients.lname)"), 'like', "%{$search}%");
        });
    }

    // Get paginated results
    $sipaRequests = $query->orderBy('clients.client_id', 'DESC')->paginate($perPage);

    // Get total count for statistics
    $totalRequests = DB::table('clients')->count();

    $sipaDetails = SipaDetail::orderBy('sipa_detail_id', 'desc')->get();

    return view('CIS.SIPARequest', [
        'sipaDetails' => $sipaDetails,
        'sipaRequests' => $sipaRequests,
        'totalRequests' => $totalRequests,
        'search' => $search,
    ]);
}

    public function showClientRequests($clientId)
    {
        $client = DB::table('clients')->where('client_id', $clientId)->first();

        if (!$client) {
            abort(404, 'Client not found.');
        }

        $clientName = trim($client->fname . ' ' . ($client->mname ?? '') . ' ' . $client->lname);

        // Only get non-archived SIPA requests
        $sipaRequests = DB::table('siparequest')
            ->where('client_id', $clientId)
            ->where('is_archived', false)
            ->select('sipa_id', 'sipa_ref_no', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('CIS.SIPARequestClient', [
            'client'        => $client,
            'clientName'    => $clientName,
            'sipaRequests'  => $sipaRequests,
            'totalRequests' => $sipaRequests->count(),
        ]);
    }

    // NEW: Show archived SIPA requests
    public function archived($clientId)
    {
        $client = DB::table('clients')->where('client_id', $clientId)->first();

        if (!$client) {
            abort(404, 'Client not found.');
        }

        $archivedSipaRequests = DB::table('siparequest')
            ->where('client_id', $clientId)
            ->where('is_archived', true)
            ->select('sipa_id', 'sipa_ref_no', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('CIS.SIPARequestClientArchived', [
            'client' => $client,
            'archivedSipaRequests' => $archivedSipaRequests,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,client_id',
            'sipa_ref_no' => 'required|string|unique:siparequest,sipa_ref_no',
            'type' => 'required|string|max:255',
        ]);

        DB::table('siparequest')->insert([
            'client_id' => $request->client_id,
            'sipa_ref_no' => $request->sipa_ref_no,
            'type' => $request->type,
            'is_archived' => false,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'SIPA request saved successfully!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sipa_ref_no' => 'required|string|unique:siparequest,sipa_ref_no,' . $id . ',sipa_id',
            'type' => 'required|string|max:255',
        ]);

        DB::table('siparequest')
            ->where('sipa_id', $id)
            ->update([
                'sipa_ref_no' => $request->sipa_ref_no,
                'type' => $request->type,
            ]);

        return response()->json([
            'message' => 'SIPA request updated successfully!'
        ]);
    }

    // Archive SIPA request
public function archive($id)
{
    $sipaRequest = SipaRequest::findOrFail($id);
    $sipaRequest->is_archived = true;
    $sipaRequest->save();

    return redirect()->back()->with('success', 'SIPA request archived successfully!');
}

// Restore SIPA request
public function restore($id)
{
    $sipaRequest = SipaRequest::findOrFail($id);
    $sipaRequest->is_archived = false;
    $sipaRequest->save();

    return redirect()->back()->with('success', 'SIPA request restored successfully!');
}

// Permanent delete
public function destroy($id)
{
    $sipaRequest = SipaRequest::findOrFail($id);
    $sipaRequest->delete();

    return redirect()->back()->with('success', 'SIPA request permanently deleted!');
}
}

