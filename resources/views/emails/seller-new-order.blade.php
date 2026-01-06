@component('mail::message')
# New Sale Received !

Hello {{ $seller->vardas }},

You have a new sale in **Order #{{ $order->id }}**.

---

## Items to ship

@foreach($items as $it)
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
<tr>
<td style="vertical-align:top;">
<strong>{{ $it['title'] }}</strong> × {{ $it['qty'] }}
</td>

<td align="right" style="width:80px;">
@if($it['image'])
<img
    src="{{ $it['image'] }}"
    width="70"
    height="70"
    style="object-fit:cover;border-radius:6px;border:1px solid #ddd;"
    alt="{{ $it['title'] }}"
>
@endif
</td>
</tr>
</table>
@endforeach

---

## Shipping address
{{ $shipping['address_line'] }}  
{{ $shipping['city'] }}, {{ $shipping['country'] }} {{ $shipping['postal_code'] }}

---

## Shipment deadline  **{{ $shipping['deadline'] }}**

---

@component('mail::button', ['url' => $shipping['shipments_url']])
Manage Shipment
@endcomponent

Thank you,  
{{ config('app.name') }}
@endcomponent
