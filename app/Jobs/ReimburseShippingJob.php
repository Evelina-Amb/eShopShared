<?php

namespace App\Jobs;

use App\Models\OrderShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\Stripe;
use Stripe\Transfer;
use Illuminate\Support\Facades\Log;
use App\Mail\BuyerShipmentShippedMail;
use Illuminate\Support\Facades\Mail;


class ReimburseShippingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $shipmentId;

    public function __construct(int $shipmentId)
    {
        $this->shipmentId = $shipmentId;
    }

    public function handle(): void
    {
        Log::info('ReimburseShippingJob started', [
        'shipment_id' => $this->shipmentId,
    ]);
        
        Stripe::setApiKey(config('services.stripe.secret'));

        $shipment = OrderShipment::where('id', $this->shipmentId)
            ->where('status', 'approved')
            ->whereNull('reimbursement_transfer_id')
            ->first();

        if (!$shipment) {
            return;
        }

        $seller = $shipment->seller;

        if (!$seller || !$seller->stripe_account_id) {
            return;
        }

        $transfer = Transfer::create([
            'amount' => $shipment->price_cents,
            'currency' => 'eur',
            'destination' => $seller->stripe_account_id,
            'metadata' => [
                'order_id' => $shipment->order_id,
                'shipment_id' => $shipment->id,
            ],
        ]);

        $shipment = OrderShipment::with(['order.user'])->findOrFail($this->shipmentId);

Mail::to($shipment->order->user->el_pastas)
    ->queue(new BuyerShipmentShippedMail($shipment));


        $shipment->update([
            'status' => 'reimbursed',
            'reimbursement_transfer_id' => $transfer->id,
        ]);
    }
}
