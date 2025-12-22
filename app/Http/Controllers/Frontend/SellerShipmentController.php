<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Jobs\ReimburseShippingJob;
use Illuminate\Http\Request;

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
            $shipment->proof_path = $request->file('proof')
                ->store('shipment_proofs', 'public');
        }

        $shipment->tracking_number = $data['tracking_number'] ?? null;

        $shipment->status = 'approved';
        $shipment->save();

        ReimburseShippingJob::dispatch($shipment->id);

        return back()->with('success', 'Shipment approved and reimbursement queued.');
    }
}
