<x-app-layout>
    <meta name="stripe-key" content="{{ config('services.stripe.key') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-5xl mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>

        <div class="grid md:grid-cols-2 gap-6">

            {{-- LEFT: SHIPPING + PAYMENT --}}
            <div class="bg-white p-6 rounded shadow">
                <form id="checkout-form">

                  <h2 class="font-semibold mb-3">Shipping address</h2>

<div class="space-y-4">

    {{-- Address --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Address
        </label>
        <input
            type="text"
            name="address"
            value="{{ old('address',
                $user->address
                    ? trim(collect([
                        $user->address->gatve,
                        $user->address->namo_nr,
                        $user->address->buto_nr ? 'Flat '.$user->address->buto_nr : null,
                    ])->filter()->implode(' '))
                    : ''
            ) }}"
            class="w-full border rounded px-3 py-2"
            required
        >
    </div>

    {{-- City --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            City
        </label>
        <input
            type="text"
            name="city"
            value="{{ old('city', $user->address->city->pavadinimas ?? '') }}"
            class="w-full border rounded px-3 py-2"
            required
        >
    </div>

    {{-- Country --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Country
        </label>
        <input
            type="text"
            name="country"
            value="{{ old('country', 'Lithuania') }}"
            class="w-full border rounded px-3 py-2"
            required
        >
    </div>

    {{-- Postal code --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Postal code
        </label>
        <input
            id="postal_code"
            class="w-full border rounded px-3 py-2"
            required
        >
    </div>

</div><br>                  
                    <h2 class="font-semibold mb-2">Shipping method</h2>

                    <select id="shipping-carrier" class="w-full border p-2 rounded mb-4">
                        <option value="">Chose a shipping method</option>
                        <option value="omniva">Omniva (parcel locker)</option>
                        <option value="venipak">Venipak (courier)</option>
                    </select>

                    <p class="text-sm text-gray-600 mb-4">
                        Each seller ships separately.
                    </p>

                    <h2 class="font-semibold mb-2">Payment</h2>

                    <div id="payment-element" class="border p-4 rounded mb-4"></div>

                    <div id="checkout-error"
                         class="hidden bg-red-100 text-red-700 p-3 mb-3 rounded">
                    </div>
                    <button id="pay-button"
                            class="bg-green-600 text-white w-full py-3 rounded font-semibold">
                        Pay
                    </button>
                </form>
            </div>

            {{-- RIGHT: ORDER SUMMARY --}}
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-semibold mb-4">Order summary</h2>

                @foreach($cartItems as $item)
                    <div class="mb-3">
                        <div class="flex justify-between">
                            <span>{{ $item->listing->pavadinimas }}</span>
                            <span>
                                {{ number_format($item->listing->kaina * $item->kiekis, 2) }} €
                            </span>
                        </div>
                        <div class="text-sm text-gray-500">
                            Seller: {{ $item->listing->user->vardas}}
                        </div>
                    </div>
                @endforeach

                <hr class="my-3">

                <div class="flex justify-between text-sm">
                    <span>Items total</span>
                    <span id="items-total">—</span>
                </div>

                <div id="small-order-row"
                     class="flex justify-between text-sm text-orange-600 hidden">
                    <span>Small order fee</span>
                    <span id="small-order-fee">—</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span>Shipping</span>
                    <span id="shipping-total">—</span>
                </div>

                <hr class="my-3">

                <div class="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span id="order-total">—</span>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/checkout.js')
</x-app-layout>
