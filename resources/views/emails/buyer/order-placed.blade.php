@component('mail::message')
# Thank you for your purchase 🎉

Hi {{ $order->user->vardas }},

Your order **#{{ $order->id }}** has been successfully placed.

---

## Items in your order
@foreach($order->orderItem as $item)
- **{{ $item->listing->pavadinimas }}** × {{ $item->kiekis }}
@endforeach

---

## Shipping address
@if($order->address && $order->address->city)
{{ $order->address->gatve ?? '' }}  
{{ $order->address->city->pavadinimas }},
{{ $order->address->city->country->pavadinimas }}
@endif

---

## Order total  
**€{{ number_format($order->bendra_suma, 2) }}**

---

You’ll receive another email once the seller ships your items.

@component('mail::button', ['url' => route('profile.edit')])
View your orders
@endcomponent

Thank you for shopping with us,  
{{ config('app.name') }}
@endcomponent
