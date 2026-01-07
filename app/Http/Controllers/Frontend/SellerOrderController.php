<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use Illuminate\Http\Request;
use App\Mail\AdminShipmentNeedsReviewMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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
     * Seller uploads tracking + proof
     */
    public function ship(Request $request, OrderShipment $shipment)
    {
        if ($shipment->seller_id !== auth()->id()) {
            abort(403);
        }

        if ($shipment->status !== 'pending') {
            return back()->with('error', 'Siunta jau pateikta.');
        }

        $data = $request->validate([
            'tracking_number' => 'required|string|max:255',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $shipment->tracking_number = $data['tracking_number'];

        $shipment->proof_path = $request
            ->file('proof')
            ->store('shipment_proofs', 'public');

        $shipment->status = 'needs_review';
        $shipment->save();

$admins = User::where('role', 'admin')->pluck('el_pastas');

if ($admins->isNotEmpty()) {
    Mail::to($admins)->send(
        new AdminShipmentNeedsReviewMail($shipment)
    );
}

        return back()->with('success', 'Siunta pateikta administratoriaus peržiūrai.');
    }
}
