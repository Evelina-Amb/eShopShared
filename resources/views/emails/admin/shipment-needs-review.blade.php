@component('mail::message')
# Shipment requires approval

A seller has submitted shipment proof and it requires review.

---

## Order
**#{{ $shipment->order_id }}**

## Seller
{{ $shipment->seller->vardas }}  
ID: {{ $shipment->seller_id }}

---

@component('mail::button', ['url' => route('admin.shipments.index')])
Review Shipments
@endcomponent

This shipment must be approved or rejected before reimbursement.

{{ config('app.name') }}
@endcomponent
