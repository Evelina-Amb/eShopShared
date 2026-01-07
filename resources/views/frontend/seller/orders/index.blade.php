<x-app-layout>
    <div class="max-w-6xl mx-auto mt-6 sm:mt-10 px-3 sm:px-0">
        <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Mano pardavimai ir siuntos</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50 hidden sm:table-header-group">
                    <tr>
                        <th class="p-3 text-left">Užsakymas</th>
                        <th class="p-3 text-left">Prekės</th>
                        <th class="p-3 text-left">Pristatymas</th>
                        <th class="p-3 text-left">Būsena</th>
                        <th class="p-3 text-left">Įkelti siuntos patvirtinimą</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($shipments as $s)
                    <tr class="border-b block sm:table-row align-top">
                        <td class="p-3 block sm:table-cell">
                            <span class="font-semibold sm:hidden">Užsakymas: </span>
                            #{{ $s->order_id }}
                        </td>

                        <td class="p-3 block sm:table-cell">
                            <span class="font-semibold sm:hidden">Prekės:</span>
                            @foreach($s->order->orderItem as $item)
                                @if($item->listing->user_id === auth()->id())
                                    <div class="flex items-center gap-3 mb-3 mt-2">
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

                        <td class="p-3 block sm:table-cell">
                            <span class="font-semibold sm:hidden">Pristatymas: </span>
                            {{ strtoupper($s->carrier) }}
                            ({{ $s->package_size }})<br>
                            €{{ number_format($s->price_cents / 100, 2) }}

@if($s->order->address && $s->order->address->city)
    <div class="text-gray-500 text-xs mt-1">
        Pristatymas:
        {{ $s->order->address->city->pavadinimas }},
        {{ $s->order->address->city->country->pavadinimas }}
    </div>
@endif
                        </td>

                        <td class="p-3 block sm:table-cell">
                            <span class="font-semibold sm:hidden">Būsena: </span>
                            @php
                                $deadline = \Carbon\Carbon::parse($s->created_at)->addDays(14);
                                $daysLeft = now()->diffInDays($deadline, false);
                            @endphp

                            @if($s->status === 'pending')
                                <div class="text-gray-500">Laukiama išsiuntimo</div>

                                @if($daysLeft >= 0)
                                    <div class="text-xs text-orange-600 mt-1">
                                        {{ $daysLeft }} Liko {{ $daysLeft === 1 ? '' : 's' }} d. išsiuntimui
                                    </div>
                                @else
                                    <div class="text-xs text-red-600 mt-1">
                                        Pristatymo terminas pasibaigė
                                    </div>
                                @endif

                            @elseif($s->status === 'needs_review')
                                <span class="text-blue-600 font-medium">Laukiama patvirtinimo</span>

                            @elseif($s->status === 'approved')
                                <span class="text-orange-600">Apdorojamas kompensavimas</span>

                            @elseif($s->status === 'reimbursed')
                                <span class="text-green-600">Užbaigta</span>

                            @else
                                <span class="text-gray-400">Nežinoma</span>
                            @endif
                        </td>

                        <td class="p-3 block sm:table-cell">
                            <span class="font-semibold sm:hidden">Siuntos patvirtinimas:</span>
                            @if($s->status === 'pending')
                                <form method="POST"
                                      action="{{ route('seller.shipments.update', $s) }}"
                                      enctype="multipart/form-data"
                                      class="space-y-2 mt-2">

                                    @csrf

                                    <input
                                        name="tracking_number"
                                        class="border p-2 rounded w-full"
                                        placeholder="Siuntos sekimo numeris"
                                    >

                                    <input
                                        type="file"
                                        name="proof"
                                        class="border p-2 rounded w-full"
                                    >

                                    <button
                                        class="bg-blue-600 text-white px-3 py-2 rounded w-full">
                                        Pateikti siuntą
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
                           Kol kas pardavimų nėra.
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
