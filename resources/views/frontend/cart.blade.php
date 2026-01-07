<x-app-layout>

<div class="max-w-4xl mx-auto mt-6 sm:mt-10 px-3 sm:px-0">

    <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-6">Mano krepšelis</h1>

    @if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

    @if($cartItems->isEmpty())
        <div class="bg-white shadow p-6 rounded text-center">
            <p class="text-gray-600">Jūsų krepšelis yra tuščias.</p>
        </div>

    @else

        {{-- CLEAR CART BUTTON --}}
        <form action="{{ route('cart.clear') }}" method="POST"
              onsubmit="return confirm('Ar tikrai norite išvalyti visą krepšelį?');">
            @csrf
            @method('DELETE')

            <button class="mb-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 w-full sm:w-auto">
                Išvalyti krepšelį
            </button>
        </form>

{{-- CART ITEMS --}}
<div class="bg-white shadow rounded p-3 sm:p-4">

    {{-- HEADER --}}
    <div class="hidden sm:grid grid-cols-12 font-semibold text-gray-600 border-b pb-2 mb-4">
        <div class="col-span-6">Prekė</div>
        <div class="col-span-2 text-right">Kaina</div>
        <div class="col-span-2 text-center">Kiekis</div>
    </div>

    @foreach($cartItems as $item)
        <div class="border-b py-4">

            <div class="flex flex-col sm:grid sm:grid-cols-12 sm:items-center gap-3 sm:gap-0">

                {{-- IMAGE + TITLE --}}
                <div class="sm:col-span-6 flex items-center gap-4 justify-center sm:justify-start">
                    @if($item->listing->photos->isNotEmpty())
                        <img
                            src="{{ asset('storage/' . $item->listing->photos->first()->failo_url) }}"
                            class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded"
                        >
                    @else
                        <img
                            src="https://via.placeholder.com/150"
                            class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded"
                        >
                    @endif

                    <a href="{{ route('listing.single', $item->listing_id) }}"
                       class="font-semibold text-blue-600 hover:underline text-center sm:text-left">
                        {{ $item->listing->pavadinimas }}
                    </a>
                </div>

                {{-- PRICE --}}
                <div class="sm:col-span-2 text-center sm:text-right font-semibold">
                    {{ number_format($item->listing->kaina, 2) }} €
                </div>

                {{-- QUANTITY --}}
                <div class="sm:col-span-2 flex justify-center items-center">

                    <form method="POST" action="{{ route('cart.decrease', $item->id) }}">
                        @csrf
                        <button 
                            class="px-3 py-1 bg-gray-200 rounded 
                                   {{ $item->kiekis <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $item->kiekis <= 1 ? 'disabled' : '' }}
                        >
                            −
                        </button>
                    </form>

                    <span class="px-4 font-semibold">{{ $item->kiekis }}</span>

                    <form method="POST" action="{{ route('cart.increase', $item->id) }}">
                        @csrf
                        <button class="px-3 py-1 bg-gray-200 rounded">+</button>
                    </form>

                </div>

                {{-- REMOVE --}}
                <form method="POST" action="{{ route('cart.remove', $item->id) }}"
                      class="sm:col-span-2 flex justify-center sm:justify-end">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 text-sm sm:text-xl hover:text-red-800">
                        Pašalinti
                    </button>
                </form>

            </div>

        </div>
    @endforeach
</div>

        {{-- TOTAL SECTION --}}
        <div class="bg-white shadow rounded p-4 sm:p-6 mt-6">
            @php
                $total = $cartItems->sum(fn($i) => $i->listing->kaina * $i->kiekis);
            @endphp

            <div class="text-lg sm:text-xl font-bold mb-4 text-center sm:text-left">
                Viso: {{ number_format($total, 2) }} €
            </div>
 {{-- fix --}}
            {{-- CHECKOUT --}}
           <form method="GET" action="{{ route('checkout.index') }}">
                <button type="submit"
                    class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 w-full">
                    Tęsti atsiskaitymą
                </button>
            </form>
        </div>
    @endif
</div>

</x-app-layout>
