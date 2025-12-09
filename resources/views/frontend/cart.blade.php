<x-app-layout>

<div class="max-w-4xl mx-auto mt-10">

    <h1 class="text-3xl font-bold mb-6">My Cart</h1>

    @if($cartItems->isEmpty())
        <div class="bg-white shadow p-6 rounded text-center">
            <p class="text-gray-600">Your cart is empty.</p>
        </div>

    @else

        {{-- CLEAR CART BUTTON --}}
        <form action="{{ route('cart.clear') }}" method="POST"
              onsubmit="return confirm('Are you sure you want to clear your entire cart?');">
            @csrf
            @method('DELETE')

            <button class="mb-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Clear Cart
            </button>
        </form>

{{-- CART ITEMS --}}
<div class="bg-white shadow rounded p-4">

    {{-- HEADER --}}
    <div class="grid grid-cols-12 font-semibold text-gray-600 border-b pb-2 mb-4">
        <div class="col-span-6">Item</div>
        <div class="col-span-2 text-right">Price</div>
        <div class="col-span-2 text-center">Quantity</div>
    </div>

    @foreach($cartItems as $item)
        <div class="grid grid-cols-12 items-center border-b py-4">

            {{-- IMAGE + TITLE --}}
            <div class="col-span-6 flex items-center gap-4">
                @if($item->listing->photos->isNotEmpty())
    <img
        src="{{ asset('storage/' . $item->listing->photos->first()->failo_url) }}"
        class="w-24 h-24 object-cover rounded"
    >
@else
    <img
        src="https://via.placeholder.com/150"
        class="w-24 h-24 object-cover rounded"
    >
@endif

                <a href="{{ route('listing.single', $item->listing_id) }}"
                   class="font-semibold text-blue-600 hover:underline">
                    {{ $item->Listing->pavadinimas }}
                </a>
            </div>

            {{-- PRICE --}}
            <div class="col-span-2 text-right font-semibold">
                {{ number_format($item->Listing->kaina, 2) }} €
            </div>

            {{-- QUANTITY --}}
            <div class="col-span-2 flex justify-center items-center">

                <form method="POST" action="{{ route('cart.decrease', $item->id) }}">
                    @csrf
                    <button class="px-2 py-1 bg-gray-200 rounded">−</button>
                </form>

                <span class="px-4">{{ $item->kiekis }}</span>

                <form method="POST" action="{{ route('cart.increase', $item->id) }}">
                    @csrf
                    <button class="px-2 py-1 bg-gray-200 rounded">+</button>
                </form>

            </div>
                
            {{-- REMOVE --}}
                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 text-xl hover:text-red-800">Remove</button>
                </form>
            </div>
    @endforeach
</div>
        {{-- TOTAL SECTION --}}
        <div class="bg-white shadow rounded p-6 mt-6">
            @php
                $total = $cartItems->sum(fn($i) => $i->Listing->kaina * $i->kiekis);
            @endphp

            <div class="text-xl font-bold mb-4">
                Total: {{ number_format($total, 2) }} €
            </div>
 {{-- fix --}}
            {{-- CHECKOUT --}}
            <form method="POST" action="{{ route('cart.checkout') }}">
                @csrf
                <button class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 w-full">
                    Continue to Checkout
                </button>
            </form>
        </div>
    @endif
</div>

</x-app-layout>
