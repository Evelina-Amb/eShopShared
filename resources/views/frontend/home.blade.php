<x-app-layout>

    <div class="container mx-auto px-4 mt-8">
        <!-- Listing Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            @forelse ($listings as $item)
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">
                    <div class="relative">

                        <img src="{{ $item->photos>first()?->failo_url ?? 'https://via.placeholder.com/300' }}" class="w-full h-48 object-cover">
                        <button
                            @click="Alpine.store('favorites').toggle({{ $item->id }})"
                            class="absolute top-2 right-2">
                            <span x-show="Alpine.store('favorites').list.includes({{ $item->id }})"
                                class="text-red-500 text-2xl">♥️</span>

                            <span x-show="!Alpine.store('favorites').list.includes({{ $item->id }})" class="text-gray-200 drop-shadow-lg text-[30px] leading-none">🤍</span>
                        </button>
                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-1">
                            {{ $item->pavadinimas }}
                        </h2>

                        <p class="text-gray-500 text-sm line-clamp-2">
                            {{ $item->aprasymas }}
                        </p>

                        <div class="flex justify-between items-center mt-3">
                            <span class="text-green-600 font-bold text-lg">
                                {{ $item->kaina }} €
                            </span>

                            <a href="/listing/{{ $item->id }}" class="text-blue-600 font-semibold">More →</a>
                        </div>
                    </div>

                </div>
            @empty
                <p class="text-gray-600 text-center">No listings found</p>
            @endforelse

        </div>

    </div>

</x-app-layout>
