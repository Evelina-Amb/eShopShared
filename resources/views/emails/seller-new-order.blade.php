@component('mail::message')
# Naujas pardavimas !

Sveiki, {{ $seller->vardas }},

Turite naują pardavimą užsakyme **Nr. {{ $order->id }}**.

---

## Prekės kurias turite išsiųsti

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

## Pristatymo adresas
{{ $shipping['address_line'] }}  
{{ $shipping['city'] }}, {{ $shipping['country'] }} {{ $shipping['postal_code'] }}

---

## Išsiuntimo terminas  **{{ $shipping['deadline'] }}**

---

@component('mail::button', ['url' => $shipping['shipments_url']])
Tvarkyti siuntą
@endcomponent

Ačiū,
{{ config('app.name') }}
@endcomponent
