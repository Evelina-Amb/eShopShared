<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Jobs\ReimburseShippingJob;
use Illuminate\Http\Request;

class ShipmentModerationController extends Controller
{
    /**
     * List shipments waiting for manual review
     */
    public function index()
    {
        $shipments = OrderShipment::with([
            'order.user',
            'seller',
            'order.orderItem.listing'
        ])
        ->where('status', 'needs_review')
        ->latest()
        ->paginate(20);

        return view('admin.shipments.index', compact('shipments'));
    }

    /**
     * Approve shipment and trigger reimbursement
     */
    public function approve(OrderShipment $shipment)
    {
        if ($shipment->status !== 'needs_review') {
            return back()->with('error', 'Neteisinga siuntos būsena.');
        }

        $shipment->update(['status' => 'approved']);

        ReimburseShippingJob::dispatch($shipment->id);

        return back()->with('success', 'Siunta patvirtinta, o kompensavimas išsiųstas.');
    }

    /**
     * Reject shipment (seller must resubmit)
     */
    public function reject(Request $request, OrderShipment $shipment)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $shipment->update([
            'status' => 'pending',
        ]);

        return back()->with('success', 'Siunta atmesta. Pardavėjas turi pateikti ją iš naujo.');
    }
}
