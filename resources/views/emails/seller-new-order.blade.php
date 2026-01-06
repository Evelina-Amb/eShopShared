<strong>HTML TEST</strong>

@component('mail::message')
# New Sale Received 🎉

Hello {{ $seller->vardas }},

You have a new sale in **Order #{{ $order->id }}**.

---

## Items to ship
@foreach($items as $it)
- **{{ $it['title'] }}** × {{ $it['qty'] }}
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
