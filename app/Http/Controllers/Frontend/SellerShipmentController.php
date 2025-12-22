<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class SellerShipment extends Controller
{
    public function ship(Request $request, OrderShipment $shipment)
{
    if ($shipment->seller_id !== auth()->id()) {
        abort(403);
    }

    $data = $request->validate([
        'tracking_number' => 'nullable|string|max:255',
        'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
    ]);

    if ($request->hasFile('proof')) {
        $path = $request->file('proof')->store('shipment_proofs', 'public');
        $shipment->proof_path = $path;
    }

    $shipment->update([
        'tracking_number' => $data['tracking_number'] ?? null,
        'status' => 'shipped',
    ]);
    
if ($shipment->tracking_number || $shipment->proof_path) {
            $shipment->update(['status' => 'approved']);
        }
    return back()->with('success', 'Shipment marked as shipped.');
}

}
