@component('mail::message')
# Jūsų užsakymas pateiktas!

Sveiki, {{ $order->user->vardas }},

Ačiū už jūsų pirkinį!  
Jūsų užsakymas **#{{ $order->id }}** sėkmingai pateiktas.

---

## Užsakymo prekės

@component('mail::table')
|  | Prekė  | Suma |
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

## Užsakymo suvestinė

<table width="100%" cellpadding="4" cellspacing="0">
<tr>
    <td>Prekių suma</td>
    <td align="right">
        €{{ number_format($order->bendra_suma, 2) }}
    </td>
</tr>

@if($order->small_order_fee_cents > 0)
<tr>
    <td>Mažo užsakymo mokestis</td>
    <td align="right">
        €{{ number_format($order->small_order_fee_cents / 100, 2) }}
    </td>
</tr>
@endif

@if($order->shipping_total_cents > 0)
<tr>
    <td>Pristatymas</td>
    <td align="right">
        €{{ number_format($order->shipping_total_cents / 100, 2) }}
    </td>
</tr>
@endif

<tr>
    <td colspan="2"><hr></td>
</tr>

<tr>
    <td><strong>Iš viso sumokėta</strong></td>
    <td align="right">
        <strong>
            €{{ number_format($order->amount_charged_cents / 100, 2) }}
        </strong>
    </td>
</tr>
</table>

---

## Pristatymo adresas
@if($order->address && $order->address->city)
{{ $order->address->gatve ?? '' }}  
{{ $order->address->city->pavadinimas }},
{{ $order->address->city->country->pavadinimas }}
@endif

---

 Kai prekės bus išsiųstos, gausite dar vieną el. laišką.

Ačiū, kad apsiperkate pas mus,
{{ config('app.name') }}
@endcomponent
