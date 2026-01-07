<?php

namespace App\Mail;

use App\Models\OrderShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminShipmentNeedsReviewMail extends Mailable
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
            ->subject('Siunta laukia peržiūros — Užsakymas #' . $this->shipment->order_id)
            ->markdown('emails.admin.shipment-needs-review');
    }
}
