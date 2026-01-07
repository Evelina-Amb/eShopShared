@component('mail::message')
# Jūsų užsakymas jau pakeliui

Sveiki, {{ $shipment->order->user->vardas }},

 Gera žinia! Dalis Jūsų užsakymo **#{{ $shipment->order_id }}** buvo išsiųsta.

---

## Išsiųstos prekės
@foreach($shipment->order->orderItem as $item)
@if($item->listing->user_id === $shipment->seller_id)
{!! '
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
    <tr>
        <td style="vertical-align:middle;">
            <strong>'.e($item->listing->pavadinimas).'</strong><br>
            <span style="color:#6b7280;">Kiekis: '.$item->kiekis.'</span>
        </td>
        <td align="right" width="70">
            '.(
                $item->listing->photos->isNotEmpty()
                ? '<img src="'.asset('storage/'.$item->listing->photos->first()->failo_url).'"
                        width="60"
                        height="60"
                        style="border-radius:6px; object-fit:cover;"
                        alt="'.e($item->listing->pavadinimas).'">'
                : ''
            ).'
        </td>
    </tr>
</table>
' !!}
@endif
@endforeach

---

@if($shipment->tracking_number)
## Siuntos sekimo numeris
**{{ $shipment->tracking_number }}**
@endif

---

## Pristatymo adresas
@if($shipment->order->address && $shipment->order->address->city)
{{ $shipment->order->address->gatve ?? '' }}  
{{ $shipment->order->address->city->pavadinimas }},
{{ $shipment->order->address->city->country->pavadinimas }}
@endif

---

Jei kas nors pasikeis, informuosime Jus papildomai.

Ačiū, kad apsiperkate pas mus, 
{{ config('app.name') }}
@endcomponent
