<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Jobs\ReimburseShippingJob;
use Illuminate\Http\Request;

class SellerOrderController extends Controller
{
    /**
     * Seller dashboard – all shipments
     */
    public function index()
    {
        $shipments = OrderShipment::with([
            'order.user',
            'order.orderItem.listing'
        ])
        ->where('seller_id', auth()->id())
        ->latest()
        ->paginate(10);

        return view('frontend.seller.orders.index', compact('shipments'));
    }

    /**
     * Seller uploads proof / tracking
     */
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

        $shipment->tracking_number = $data['tracking_number'] ?? null;
        $shipment->status = 'approved';
        $shipment->save();

        // 🔁 Dispatch reimbursement
        ReimburseShippingJob::dispatch($shipment->id);

        return back()->with('success', 'Shipment submitted. Reimbursement processing.');
    }
}
