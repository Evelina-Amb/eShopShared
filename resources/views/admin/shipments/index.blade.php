<x-app-layout>
<div class="max-w-6xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Shipment moderation</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-3">Order</th>
                    <th class="p-3">Seller</th>
                    <th class="p-3">Buyer</th>
                    <th class="p-3">Carrier</th>
                    <th class="p-3">Proof</th>
                    <th class="p-3">Tracking</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse($shipments as $s)
                <tr class="border-b">
                    <td class="p-3">#{{ $s->order_id }}</td>
                    <td class="p-3">{{ $s->seller->name }}</td>
                    <td class="p-3">{{ $s->order->user->name }}</td>
                    <td class="p-3">
                        {{ strtoupper($s->carrier) }} ({{ $s->package_size }})
                        <br>€{{ number_format($s->price_cents / 100, 2) }}
                    </td>
                    <td class="p-3">
                        @if($s->proof_path)
                            <a href="{{ asset('storage/'.$s->proof_path) }}"
                               target="_blank"
                               class="text-blue-600 underline">
                                View proof
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="p-3">
                        {{ $s->tracking_number ?? '—' }}
                    </td>
                    <td class="p-3 flex gap-2">
                        <form method="POST" action="{{ route('admin.shipments.approve', $s) }}">
                            @csrf
                            <button class="bg-green-600 text-white px-3 py-1 rounded">
                                Approve
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.shipments.reject', $s) }}">
                            @csrf
                            <button class="bg-red-600 text-white px-3 py-1 rounded">
                                Reject
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        No shipments waiting for review.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $shipments->links() }}</div>
</div>
</x-app-layout>
