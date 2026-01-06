<p>Hello {{ $seller->vardas }},</p>

<p>You have a new sale in order <strong>#{{ $order->id }}</strong>.</p>

<p><strong>Items to ship:</strong></p>
<ul>
@foreach($items as $it)
    <li>
        {{ $it['title'] }} × {{ $it['qty'] }}
    </li>
@endforeach
</ul>

<p><strong>Ship to:</strong><br>
{{ $shipping['address_line'] }}<br>
{{ $shipping['city'] }}, {{ $shipping['country'] }} {{ $shipping['postal_code'] }}
</p>

<p><strong>Shipment deadline:</strong> {{ $shipping['deadline'] }}</p>

<p>
You can manage this shipment here:<br>
<a href="{{ $shipping['shipments_url'] }}">{{ $shipping['shipments_url'] }}</a>
</p>

<p>Thank you.</p>
