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

    // Save proof (but do NOT approve automatically)
    if ($request->hasFile('proof')) {
        $shipment->proof_path = $request->file('proof')->store('shipment_proofs', 'public');
    }

    // Save tracking number
    $shipment->tracking_number = $data['tracking_number'] ?? null;

    // Decide status:
    // tracking only and passes basic validation -> approved (auto)
    // proof uploaded OR tracking invalid -> needs_review (manual)
    if ($shipment->tracking_number && $this->trackingLooksValid($shipment->carrier, $shipment->tracking_number)) {
        $shipment->status = 'approved';
        $shipment->save();

        // dispatch reimbursement only when approved
        \App\Jobs\ReimburseShippingJob::dispatch($shipment->id);

        return back()->with('success', 'Tracking accepted. Reimbursement processing.');
    }

    // Anything else: needs manual review
    $shipment->status = 'needs_review';
    $shipment->save();

    return back()->with('success', 'Shipment submitted for review.');
}


private function trackingLooksValid(string $carrier, string $tracking): bool
{
    $tracking = trim($tracking);

    if (strlen($tracking) < 6 || strlen($tracking) > 40) return false;
    if (!preg_match('/^[A-Za-z0-9\-]+$/', $tracking)) return false;

    if ($carrier === 'omniva') {
        // Accept 2 letters + 9 digits + 2 letters, OR between 10-20 char
        return (bool) preg_match('/^[A-Z]{2}\d{9}[A-Z]{2}$/i', $tracking) || (strlen($tracking) >= 10 && strlen($tracking) <= 20);
    }

    if ($carrier === 'venipak') {
        // Accept numeric 8-20
        return (bool) preg_match('/^[0-9]{8,20}$/', $tracking) || (strlen($tracking) >= 8 && strlen($tracking) <= 20);
    }

    return true;
}

}
