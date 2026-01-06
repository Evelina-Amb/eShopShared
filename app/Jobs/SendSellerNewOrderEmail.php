<?php

namespace App\Jobs;

use App\Mail\SellerNewOrderMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSellerNewOrderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $orderId,
        public int $sellerId
    ) {}

    public function handle(): void
    {
        $order = Order::with(['orderItem.listing.photos', 'address.city.country'])->findOrFail($this->orderId);
        $seller = User::findOrFail($this->sellerId);

        $items = [];
        foreach ($order->orderItem as $item) {
            if ((int)$item->listing->user_id !== (int)$seller->id) continue;

            $items[] = [
                'title' => $item->listing->pavadinimas,
                'qty'   => $item->kiekis,
                 'image' => $item->listing->photos->isNotEmpty()
        ? asset('storage/' . $item->listing->photos->first()->failo_url)
        : null,
            ];
        }

        if (empty($items)) return;

        $addressLine = '';
        if ($order->address) {
            $addressLine = trim(collect([
                $order->address->gatve ?? null,
                $order->address->namo_nr ?? null,
                $order->address->buto_nr ? 'Flat '.$order->address->buto_nr : null,
            ])->filter()->implode(' '));
        }

        $city = $order->address?->city?->pavadinimas ?? '';
        $country = $order->address?->city?->country?->pavadinimas ?? '';
        $postal = $order->postal_code ?? ($order->shipping_postal_code ?? '');

        $deadline = $order->created_at->copy()->addDays(14)->toDateString();

        $shipmentsUrl = route('seller.orders');
        Mail::to($seller->el_pastas)->send(
            new SellerNewOrderMail($order, $seller, $items, [
                'address_line' => $addressLine,
                'city' => $city,
                'country' => $country,
                'postal_code' => $postal,
                'deadline' => $deadline,
                'shipments_url' => $shipmentsUrl,
            ])
        );
    }
}
