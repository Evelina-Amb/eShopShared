<x-app-layout>
    <div class="max-w-4xl mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- LEFT --}}
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-xl font-semibold mb-4">Shipping address</h2>

                <form id="checkout-form">
                    @csrf

                    <div class="mb-3">
                        <label class="block text-sm text-gray-600 mb-1">Address</label>
                        <input id="address" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-600 mb-1">City</label>
                        <input id="city" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm text-gray-600 mb-1">Postal code</label>
                        <input id="postal_code" class="w-full border rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">Country</label>
                        <input id="country" class="w-full border rounded p-2" required>
                    </div>

                    <h2 class="text-xl font-semibold mb-3">Payment</h2>

                    <div class="border rounded p-3 mb-3">
                        <div id="payment-element"></div>
                    </div>

                    <div id="checkout-error"
                         class="hidden bg-red-100 text-red-800 p-3 rounded mb-3"></div>

                    <button id="pay-button"
                            type="submit"
                            class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 w-full">
                        Pay {{ number_format($total, 2) }} €
                    </button>
                </form>
            </div>

            {{-- RIGHT --}}
            <div class="bg-white shadow rounded p-6">
                <h2 class="text-xl font-semibold mb-4">Order summary</h2>

                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between border-b pb-2">
                            <div>
                                <div class="font-semibold">
                                    {{ $item->listing->pavadinimas }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    Qty: {{ $item->kiekis }}
                                </div>
                            </div>
                            <div class="font-semibold">
                                {{ number_format($item->listing->kaina * $item->kiekis, 2) }} €
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-xl font-bold">
                    Total: {{ number_format($total, 2) }} €
                </div>
            </div>
        </div>
    </div>

    {{-- Stripe --}}
    <script src="https://js.stripe.com/v3/"></script>
    @vite('resources/js/checkout.js')
</x-app-layout>
