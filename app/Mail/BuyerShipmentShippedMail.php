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
            ->subject('Jūsų užsakymas #' . $this->shipment->order_id . ' buvo išsiųstas')
            ->markdown('emails.buyer.shipment-shipped');
    }
}
