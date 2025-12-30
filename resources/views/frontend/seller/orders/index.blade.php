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
                        <th class="p-3 text-left">Buyer</th>
                        <th class="p-3 text-left">Items</th>
                        <th class="p-3 text-left">Shipping</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Upload shippment proof</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($shipments as $s)
                    <tr class="border-b">
                        <td class="p-3">#{{ $s->order_id }}</td>

                        <td class="p-3">
                            {{ $s->order->user->name ?? 'Buyer' }}
                        </td>

                        <td class="p-3">
                            @foreach($s->order->orderItem as $item)
                                @if($item->listing->user_id === auth()->id())
                                    <div>
                                        {{ $item->listing->pavadinimas }}
                                        × {{ $item->kiekis }}
                                    </div>
                                @endif
                            @endforeach
                        </td>

                        <td class="p-3">
                            {{ strtoupper($s->carrier) }}
                            ({{ $s->package_size }})<br>
                            €{{ number_format($s->price_cents / 100, 2) }}
                        </td>

                        <td class="p-3">
                            @if($s->status === 'pending')
                                <span class="text-gray-500">Waiting to ship</span>
                            @elseif($s->status === 'approved')
                                <span class="text-orange-600">Processing reimbursement</span>
                            @elseif($s->status === 'reimbursed')
                                <span class="text-green-600">Completed</span>
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
