<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class TripClientController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');
    $perPage = 10;

    // Build the query
    $query = Client::where('is_archived', false);

    // Apply search filter
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('fname', 'like', "%{$search}%")
              ->orWhere('mname', 'like', "%{$search}%")
              ->orWhere('lname', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('contact', 'like', "%{$search}%");
        });
    }

    // Get paginated results
    $clients = $query->orderBy('client_id', 'desc')->paginate($perPage);

    // Transform the paginated collection
    $clients->getCollection()->transform(function($client) {
        return (object)[
            'client_id' => $client->client_id,
            'client_name' => $client->company_name ?? 'N/A',
            'address' => $client->address ?? 'N/A',
            'contact_person' => trim(($client->fname ?? '') . ' ' . ($client->mname ?? '') . ' ' . ($client->lname ?? '')) ?: 'N/A'
        ];
    });

    return view('TD.TripClient', compact('clients', 'search'));
}
}