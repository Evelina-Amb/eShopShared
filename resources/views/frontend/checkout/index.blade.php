<x-app-layout>
    <meta name="stripe-key" content="{{ config('services.stripe.key') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-4xl mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>

        <div class="grid md:grid-cols-2 gap-6">

            {{-- LEFT: PAYMENT --}}
            <div class="bg-white p-6 rounded shadow">
                <form id="checkout-form">
                    <input id="address" class="w-full border p-2 mb-2" placeholder="Address" required>
                    <input id="city" class="w-full border p-2 mb-2" placeholder="City" required>
                    <input id="postal_code" class="w-full border p-2 mb-2" placeholder="Postal code" required>
                    <input id="country" class="w-full border p-2 mb-4" placeholder="Country" required>

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
                    <div class="flex justify-between mb-2">
                        <span>{{ $item->listing->pavadinimas }}</span>
                        <span>
                            {{ number_format($item->listing->kaina * $item->kiekis, 2) }} €
                        </span>
                    </div>
                @endforeach

                <hr class="my-3">

                {{-- Dynamic totals filled by JS --}}
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
                    <span>Shipping total</span>
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
