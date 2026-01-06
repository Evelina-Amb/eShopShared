<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Illuminate\Http\Request;

class SellerShipment extends Controller
{
    public function ship(Request $request, OrderShipment $shipment)
    {
        if ($shipment->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($shipment->status !== 'pending') {
            return back()->with('error', 'Shipment already processed.');
        }

        $data = $request->validate([
            'tracking_number' => 'required|string|max:255',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $shipment->proof_path = $request
            ->file('proof')
            ->store('shipment_proofs', 'public');

        $shipment->tracking_number = $data['tracking_number'];
        $shipment->status = 'needs_review';
        $shipment->save();

        return back()->with(
            'success',
            'Shipment submitted and sent for admin review.'
        );
    }
}
