<x-app-layout>
    <div class="max-w-6xl mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-6">My purchases</h1>

        @forelse($orders as $order)
            <div class="bg-white shadow rounded mb-6 p-5">
                <div class="flex justify-between mb-3">
                    <div>
                        <div class="font-semibold">Order #{{ $order->id }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $order->pirkimo_data?->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    <div class="font-semibold">
                        €{{ number_format($order->amount_charged_cents / 100, 2) }}
                    </div>
                </div>

                {{-- ITEMS --}}
<div class="border-t pt-3">
    @foreach($order->orderItem as $item)
        <div class="flex justify-between text-sm mb-1">
            <span>
                {{ $item->Listing->pavadinimas }}
                <span class="text-gray-500">
                    (Seller: {{ $item->Listing->user->vardas }})
                </span>
            </span>
            <span>
                €{{ number_format($item->kaina * $item->kiekis, 2) }}
            </span>
        </div>
    @endforeach
</div>
                {{-- SHIPMENTS --}}
                <div class="border-t mt-3 pt-3 space-y-2">
                    @foreach($order->shipments as $shipment)
                        <div class="text-sm flex justify-between items-center">
                            <div>
                                <span class="font-medium">
                                    Shipment from {{ $shipment->seller->name }}
                                </span>
                                <span class="text-gray-500">
                                    ({{ strtoupper($shipment->carrier) }})
                                </span>
                            </div>

                            <div>
                                @if($shipment->status === 'pending')
                                    <span class="px-2 py-1 text-xs rounded bg-gray-200">
                                        Waiting to be shipped
                                    </span>
                                @elseif(in_array($shipment->status, ['approved','reimbursed']))
                                    <span class="px-2 py-1 text-xs rounded bg-green-200">
                                        Shipped
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($shipment->tracking_number)
                            <div class="text-xs text-gray-600 ml-2">
                                Tracking: {{ $shipment->tracking_number }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white shadow rounded p-6 text-center text-gray-600">
                You haven’t purchased anything yet.
            </div>
        @endforelse

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
