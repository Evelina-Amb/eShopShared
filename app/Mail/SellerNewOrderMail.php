<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerNewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public User $seller,
        public array $items,
        public array $shipping
    ) {}

    public function build()
    {
        return $this
            ->subject('New sale: Order #' . $this->order->id)
            ->markdown('emails.seller-new-order');
    }
}
