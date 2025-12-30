<x-app-layout>
  <div class="max-w-5xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">My shipments</h1>

    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded">
      <table class="w-full text-sm">
        <thead class="border-b">
          <tr class="text-left">
            <th class="p-3">Order</th>
            <th class="p-3">Carrier</th>
            <th class="p-3">Size</th>
            <th class="p-3">Shipping paid</th>
            <th class="p-3">Status</th>
            <th class="p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($shipments as $s)
            <tr class="border-b">
              <td class="p-3">#{{ $s->order_id }}</td>
              <td class="p-3">{{ strtoupper($s->carrier) }}</td>
              <td class="p-3">{{ $s->package_size }}</td>
              <td class="p-3">€{{ number_format($s->price_cents / 100, 2) }}</td>
              <td class="p-3">
                <span class="px-2 py-1 rounded bg-gray-100">{{ $s->status }}</span>
              </td>
              <td class="p-3">
                @if(in_array($s->status, ['pending','shipped']))
                  <form method="POST" action="{{ route('seller.shipments.update', $s) }}" enctype="multipart/form-data" class="flex gap-2 items-center">
                    @csrf
                    <input name="tracking_number" class="border p-1 rounded" placeholder="Tracking (optional)" />
                    <input type="file" name="proof" class="border p-1 rounded" />
                    <button class="bg-blue-600 text-white px-3 py-1 rounded">Submit</button>
                  </form>
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr><td class="p-3" colspan="6">No shipments yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $shipments->links() }}</div>
  </div>
</x-app-layout>
