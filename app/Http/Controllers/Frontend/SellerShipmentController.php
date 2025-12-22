<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Illuminate\Http\Request;

class SellerShipmentController extends Controller
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
            $shipment->proof_path = $request
                ->file('proof')
                ->store('shipment_proofs', 'public');
        }

        if (!empty($data['tracking_number'])) {
            $shipment->tracking_number = $data['tracking_number'];
        }

        if ($shipment->tracking_number || $shipment->proof_path) {
            $shipment->status = 'approved';
        } else {
            $shipment->status = 'pending';
        }

        $shipment->save();

        return back()->with('success', 'Shipment submitted successfully.');
    }
}
