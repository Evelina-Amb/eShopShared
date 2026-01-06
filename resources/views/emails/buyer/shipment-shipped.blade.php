@component('mail::message')
# Your order is on the way

Hi {{ $shipment->order->user->vardas }},

Good news! One part of your order **#{{ $shipment->order_id }}** has been shipped.

---

## Items shipped
@foreach($shipment->order->orderItem as $item)
@if($item->listing->user_id === $shipment->seller_id)
- **{{ $item->listing->pavadinimas }}** × {{ $item->kiekis }}
@endif
@endforeach

---

@if($shipment->tracking_number)
## Tracking number
**{{ $shipment->tracking_number }}**
@endif

---

## Delivery address
@if($shipment->order->address && $shipment->order->address->city)
{{ $shipment->order->address->gatve ?? '' }}  
{{ $shipment->order->address->city->pavadinimas }},
{{ $shipment->order->address->city->country->pavadinimas }}
@endif

---

You’ll receive another update if anything changes.

Thank you for shopping with us,  
{{ config('app.name') }}
@endcomponent
