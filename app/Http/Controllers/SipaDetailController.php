<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sipadetail;

class SipadetailController extends Controller
{
    /**
     * Get all rates for a specific SIPA request.
     * Returns JSON for AJAX.
     */
    public function index($sipa_id)
    {
        $rates = Sipadetail::where('sipa_id', $sipa_id)
            ->orderBy('sipa_detail_id', 'desc')
            ->get();

        return response()->json($rates);
    }

    /**
     * Store a new rate for a SIPA.
     */
    public function store(Request $request)
{
    $request->validate([
        'sipa_id' => 'required|exists:siparequest,sipa_id',
        'size' => 'required|string',
        'volume' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'route_from' => 'required|string',
        'route_to' => 'required|string',
        'effectivity_from' => 'required|date',
        'effectivity_to' => 'required|date|after_or_equal:effectivity_from',
    ]);

    // Check if the latest rate is expired (ONLY blocks new additions)
    $latestRate = Sipadetail::where('sipa_id', $request->sipa_id)
        ->orderBy('effectivity_to', 'desc')
        ->first();

    if ($latestRate && now()->gt($latestRate->effectivity_to)) {
        return response()->json([
            'message' => 'Cannot add rate. The latest rate expired on ' . $latestRate->effectivity_to . '.',
            'expired' => true
        ], 422);
    }

    $rate = Sipadetail::create($request->only([
        'sipa_id', 'size', 'volume', 'price', 'route_from', 'route_to', 'effectivity_from', 'effectivity_to'
    ]));

    return response()->json([
        'message' => 'Rate added successfully',
        'rate' => $rate
    ]);
}

        public function show($sipaId)
    {
        return Sipadetail::where('sipa_id', $sipaId)->get();
    }
    /**
     * Update an existing rate.
     * Uses route-model binding for $rate.
     */
    public function update(Request $request, Sipadetail $sipadetail)
{
    $request->validate([
        'size' => 'required|string',
        'volume' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'route_from' => 'required|string',
        'route_to' => 'required|string',
        'effectivity_from' => 'required|date',
        'effectivity_to' => 'required|date|after_or_equal:effectivity_from',
    ]);

    // Check if the latest rate is expired (ONLY blocks edits)
    $latestRate = Sipadetail::where('sipa_id', $sipadetail->sipa_id)
        ->orderBy('effectivity_to', 'desc')
        ->first();

    if ($latestRate && now()->gt($latestRate->effectivity_to)) {
        return response()->json([
            'message' => 'Cannot edit rate. The latest rate expired on ' . $latestRate->effectivity_to . '.',
            'expired' => true
        ], 422);
    }

    $sipadetail->update($request->only([
        'size', 'volume', 'price', 'route_from', 'route_to', 'effectivity_from', 'effectivity_to'
    ]));

    $sipadetail->refresh();

    return response()->json([
        'message' => 'Rate updated successfully',
        'rate' => $sipadetail->toArray()
    ]);
}
    public function destroy($id)
{
    $rate = Sipadetail::findOrFail($id);
    $rate->delete();

    return response()->json([
        'message' => 'Rate deleted successfully'
    ]);

    
}
}
