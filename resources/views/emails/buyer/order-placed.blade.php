@component('mail::message')
# Your order has been placed !

Hi {{ $order->user->vardas }},

Thank you for your purchase!  
Your order **#{{ $order->id }}** has been successfully placed.

---

## Items in your order

@component('mail::table')
|  | Item | Total |
|:--|:-----|------:|
@foreach($order->orderItem as $item)
| 
@if($item->listing->photos->isNotEmpty())
<img src="{{ asset('storage/' . $item->listing->photos->first()->failo_url) }}" width="60" style="border-radius:6px;border:1px solid #ddd;">
@endif
| **{{ $item->listing->pavadinimas }}**  
€{{ number_format($item->kaina, 2) }} × {{ $item->kiekis }}
| **€{{ number_format($item->kaina * $item->kiekis, 2) }}**
@endforeach
@endcomponent

---

## Order summary

<table width="100%" cellpadding="4" cellspacing="0">
<tr>
    <td>Items total</td>
    <td align="right">
        €{{ number_format($order->bendra_suma, 2) }}
    </td>
</tr>

@if($order->small_order_fee_cents > 0)
<tr>
    <td>Small order fee</td>
    <td align="right">
        €{{ number_format($order->small_order_fee_cents / 100, 2) }}
    </td>
</tr>
@endif

@if($order->shipping_total_cents > 0)
<tr>
    <td>Shipping</td>
    <td align="right">
        €{{ number_format($order->shipping_total_cents / 100, 2) }}
    </td>
</tr>
@endif

<tr>
    <td colspan="2"><hr></td>
</tr>

<tr>
    <td><strong>Total paid</strong></td>
    <td align="right">
        <strong>
            €{{ number_format($order->amount_charged_cents / 100, 2) }}
        </strong>
    </td>
</tr>
</table>

---

## Shipping address
@if($order->address && $order->address->city)
{{ $order->address->gatve ?? '' }}  
{{ $order->address->city->pavadinimas }},
{{ $order->address->city->country->pavadinimas }}
@endif

---

You’ll receive another email when your items are shipped.

Thank you for shopping with us,  
{{ config('app.name') }}
@endcomponent
