<x-app-layout>
    <div class="max-w-6xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6">My Sales & Shipments</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded">
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Order</th>
                        <th class="p-3 text-left">Items</th>
                        <th class="p-3 text-left">Shipping</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Upload shippment proof</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($shipments as $s)
                    <tr class="border-b align-top">
                        <td class="p-3">#{{ $s->order_id }}</td>

                        <td class="p-3">
                            @foreach($s->order->orderItem as $item)
                                @if($item->listing->user_id === auth()->id())
                                    <div class="flex items-center gap-3 mb-3">
                                        <img
                                            src="{{ $item->listing->photos->isNotEmpty()
                                                ? asset('storage/' . $item->listing->photos->first()->failo_url)
                                                : 'https://via.placeholder.com/60x60?text=No+Image'
                                            }}"
                                            class="w-14 h-14 object-cover rounded border"
                                        >

                                        <div>
                                            <div class="font-medium">
                                                {{ $item->listing->pavadinimas }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                × {{ $item->kiekis }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </td>

                        <td class="p-3">
                            {{ strtoupper($s->carrier) }}
                            ({{ $s->package_size }})<br>
                            €{{ number_format($s->price_cents / 100, 2) }}

@if($s->order->address && $s->order->address->city)
    <div class="text-gray-500 text-xs mt-1">
        Delivery:
        {{ $s->order->address->city->pavadinimas }},
        {{ $s->order->address->city->country->pavadinimas }}
    </div>
@endif
</td>

                        <td class="p-3">
                            @php
                                $deadline = \Carbon\Carbon::parse($s->created_at)->addDays(14);
                                $daysLeft = now()->diffInDays($deadline, false);
                            @endphp

                            @if($s->status === 'pending')
                                <div class="text-gray-500">Waiting to ship</div>

                                @if($daysLeft >= 0)
                                    <div class="text-xs text-orange-600 mt-1">
                                        ⏱ {{ $daysLeft }} day{{ $daysLeft === 1 ? '' : 's' }} left to ship
                                    </div>
                                @else
                                    <div class="text-xs text-red-600 mt-1">
                                        Shipping deadline passed
                                    </div>
                                @endif

                            @elseif($s->status === 'needs_review')
                                <span class="text-blue-600 font-medium">Waiting for approval</span>

                            @elseif($s->status === 'approved')
                                <span class="text-orange-600">Processing reimbursement</span>

                            @elseif($s->status === 'reimbursed')
                                <span class="text-green-600">Completed</span>

                            @else
                                <span class="text-gray-400">Unknown</span>
                            @endif
                        </td>

                        <td class="p-3">
                            @if($s->status === 'pending')
                                <form method="POST"
                                      action="{{ route('seller.shipments.update', $s) }}"
                                      enctype="multipart/form-data"
                                      class="space-y-2">

                                    @csrf

                                    <input
                                        name="tracking_number"
                                        class="border p-1 rounded w-full"
                                        placeholder="Tracking number (optional)"
                                    >

                                    <input
                                        type="file"
                                        name="proof"
                                        class="border p-1 rounded w-full"
                                    >

                                    <button
                                        class="bg-blue-600 text-white px-3 py-1 rounded w-full">
                                        Submit shipment
                                    </button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">
                            No sales yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $shipments->links() }}
        </div>
    </div>
</x-app-layout>
