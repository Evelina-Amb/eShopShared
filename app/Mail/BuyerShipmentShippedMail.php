<?php

namespace App\Mail;

use App\Models\OrderShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BuyerShipmentShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public OrderShipment $shipment;

    public function __construct(OrderShipment $shipment)
    {
        $this->shipment = $shipment;
    }

    public function build()
    {
        return $this
            ->subject('Your order #' . $this->shipment->order_id . ' has been shipped')
            ->markdown('emails.buyer.shipment-shipped');
    }
}
