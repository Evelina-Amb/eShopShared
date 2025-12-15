<x-app-layout>
    <div class="max-w-2xl mx-auto mt-10">
        <div class="bg-white shadow rounded p-8 text-center">
            <h1 class="text-3xl font-bold mb-3">Payment successful</h1>
            <p class="text-gray-600 mb-6">
                Thank you! Your order was placed successfully.
            </p>

            <a href="{{ route('home') }}"
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700">
                Back to home
            </a>
        </div>
    </div>
</x-app-layout>
