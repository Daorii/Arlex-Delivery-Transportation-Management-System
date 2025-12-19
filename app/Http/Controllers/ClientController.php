<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
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
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('contact', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%");
        });
    }

    // Get paginated clients (ordered by client_id instead of created_at)
    $clients = $query->orderBy('client_id', 'desc')->paginate($perPage);

    // Get statistics (from all non-archived clients, not just current page)
    $allClients = Client::where('is_archived', false)->get();
    $totalClients = $allClients->count();
    $regularClients = $allClients->where('status', 'regular')->count();
    $occasionalClients = $allClients->where('status', 'occasional')->count();
    
    // Set newClients to 0 since created_at doesn't exist in your table
    $newClients = 0;

    return view('CIS.clients', compact('clients', 'totalClients', 'regularClients', 'occasionalClients', 'newClients', 'search'));
}

    public function archived()
    {
        // Get only archived clients
        $archivedClients = Client::where('is_archived', true)->get();
        
        return view('CIS.clients_archived', compact('archivedClients'));
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact' => 'required|numeric|digits_between:10,15',
            'email' => 'required|email|unique:clients,email',
        ], [
            'contact.numeric' => 'Contact number must contain only numbers.',
            'contact.digits_between' => 'Contact number must be between 10 and 15 digits.',
            'email.unique' => 'This email address is already registered.',
        ]);

        $client = new Client();
        $client->fname = $validated['first_name'];
        $client->mname = $validated['middle_name'];
        $client->lname = $validated['last_name'];
        $client->company_name = $validated['company_name'];
        $client->address = $validated['address'];
        $client->contact = $validated['contact'];
        $client->email = $validated['email'];
        $client->is_archived = false;
        $client->save();

        return redirect()->route('clients.index')->with('success', 'Client added successfully!');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact' => 'required|numeric|digits_between:10,15',
            'email' => 'required|email|unique:clients,email,' . $client->client_id . ',client_id',
        ], [
            'contact.numeric' => 'Contact number must contain only numbers.',
            'contact.digits_between' => 'Contact number must be between 10 and 15 digits.',
            'email.unique' => 'This email address is already registered.',
        ]);

        $client->update([
            'fname' => $validated['first_name'],
            'mname' => $validated['middle_name'],
            'lname' => $validated['last_name'],
            'company_name' => $validated['company_name'],
            'address' => $validated['address'],
            'contact' => $validated['contact'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
    }

    // Archive instead of delete
    public function archive($id)
{
    // Debug: Log the ID
    \Log::info('Archiving client ID: ' . $id);
    
    try {
        $client = Client::findOrFail($id);
        
        // Debug: Log before update
        \Log::info('Client found: ' . $client->fname . ' ' . $client->lname);
        \Log::info('Current is_archived status: ' . $client->is_archived);
        
        $client->update(['is_archived' => true]);
        
        // Debug: Log after update
        \Log::info('After update is_archived status: ' . $client->fresh()->is_archived);
        
        return redirect()->route('clients.index')->with('success', 'Client archived successfully!');
        
    } catch (\Exception $e) {
        // Debug: Log any errors
        \Log::error('Archive error: ' . $e->getMessage());
        return redirect()->route('clients.index')->with('error', 'Failed to archive client: ' . $e->getMessage());
    }
}

public function restore($id)
{
    $client = Client::findOrFail($id);
    $client->update(['is_archived' => false]);
    return redirect()->route('clients.archived')->with('success', 'Client restored successfully!');
}

    // Permanent delete (only from archived)
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.archived')->with('success', 'Client permanently deleted!');
    }
}