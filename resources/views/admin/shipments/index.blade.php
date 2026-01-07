<x-app-layout>
<div class="max-w-6xl mx-auto mt-6 sm:mt-10 px-3 sm:px-0">
    <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6">Siuntų moderavimas</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b hidden sm:table-header-group">
                <tr>
                    <th class="p-3 text-left">Užsakymas</th>
                    <th class="p-3 text-left">Pardavėjas</th>
                    <th class="p-3 text-left">Pirkėjas</th>
                    <th class="p-3 text-left">Vežėjas</th>
                    <th class="p-3 text-left">Įrodymas</th>
                    <th class="p-3 text-left">Siuntos sekimas</th>
                    <th class="p-3 text-left">Veiksmai</th>
                </tr>
            </thead>

            <tbody>
            @forelse($shipments as $s)
                <tr class="border-b block sm:table-row">
                    <td class="p-3 block sm:table-cell">
                        <span class="font-semibold sm:hidden">Užsakymas: </span>
                        #{{ $s->order_id }}
                    </td>

                    <td class="p-3 block sm:table-cell">
                        <span class="font-semibold sm:hidden">Pardavėjas: </span>
                        {{ $s->seller->name }}
                    </td>

                    <td class="p-3 block sm:table-cell">
                        <span class="font-semibold sm:hidden">Pirkėjas: </span>
                        {{ $s->order->user->name }}
                    </td>

                    <td class="p-3 block sm:table-cell">
                        <span class="font-semibold sm:hidden">Vežėjas: </span>
                        {{ strtoupper($s->carrier) }} ({{ $s->package_size }})
                        <br>
                        €{{ number_format($s->price_cents / 100, 2) }}
                    </td>

                    <td class="p-3 block sm:table-cell">
                        <span class="font-semibold sm:hidden">Įrodymas: </span>
                        @if($s->proof_path)
                            <a href="{{ asset('storage/'.$s->proof_path) }}"
                               target="_blank"
                               class="text-blue-600 underline">
                                Peržiūrėti įrodymą
                            </a>
                        @else
                            —
                        @endif
                    </td>

                    <td class="p-3 block sm:table-cell">
                        <span class="font-semibold sm:hidden">Siuntos sekimas: </span>
                        {{ $s->tracking_number ?? '—' }}
                    </td>

                    <td class="p-3 block sm:table-cell">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <form method="POST" action="{{ route('admin.shipments.approve', $s) }}">
                                @csrf
                                <button class="bg-green-600 text-white px-3 py-1 rounded w-full sm:w-auto">
                                    Patvirtinti
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.shipments.reject', $s) }}">
                                @csrf
                                <button class="bg-red-600 text-white px-3 py-1 rounded w-full sm:w-auto">
                                    Atmesti
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        Nėra siuntų, laukiančių peržiūros.
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
