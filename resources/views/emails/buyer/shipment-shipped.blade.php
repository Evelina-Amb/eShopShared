@component('mail::message')
# Your order is on the way

Hi {{ $shipment->order->user->vardas }},

Good news! One part of your order **#{{ $shipment->order_id }}** has been shipped.

---

## Items shipped
@foreach($shipment->order->orderItem as $item)
@if($item->listing->user_id === $shipment->seller_id)
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
    <tr>
        <td style="vertical-align:middle;">
            <strong>{{ $item->listing->pavadinimas }}</strong><br>
            <span style="color:#6b7280;">Quantity: {{ $item->kiekis }}</span>
        </td>

        <td align="right" width="70">
            @if($item->listing->photos->isNotEmpty())
                <img
                    src="{{ asset('storage/' . $item->listing->photos->first()->failo_url) }}"
                    width="60"
                    height="60"
                    style="border-radius:6px; object-fit:cover;"
                    alt="{{ $item->listing->pavadinimas }}"
                >
            @endif
        </td>
    </tr>
</table>
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
