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

class ReimburseShippingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $shipments = OrderShipment::where('status', 'approved')
            ->whereNull('reimbursement_transfer_id')
            ->get();

        foreach ($shipments as $shipment) {
            $seller = $shipment->seller;

            if (!$seller || !$seller->stripe_account_id) {
                continue;
            }

            $transfer = Transfer::create([
                'amount' => $shipment->shipping_cents,
                'currency' => 'eur',
                'destination' => $seller->stripe_account_id,
                'metadata' => [
                    'order_id' => $shipment->order_id,
                    'shipment_id' => $shipment->id,
                ],
            ]);

            $shipment->update([
                'status' => 'reimbursed',
                'reimbursement_transfer_id' => $transfer->id,
            ]);
        }
    }
}
