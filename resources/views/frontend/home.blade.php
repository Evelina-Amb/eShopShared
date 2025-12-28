<x-app-layout>

<div
    x-data
    x-init="Alpine.store('favorites').load()"
    class="container mx-auto px-4 mt-8"
>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

        @forelse ($listings as $item)
            <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                <!-- IMAGE + HEART -->
                <div class="relative">

                    @if($item->photos->isNotEmpty())
                        <img
                            src="{{ asset('storage/' . $item->photos->first()->failo_url) }}"
                            class="w-full h-48 object-cover"
                        >
                    @else
                        <img
                            src="https://via.placeholder.com/300"
                            class="w-full h-48 object-cover"
                        >
                    @endif

                    @auth
                        @if(auth()->id() !== $item->user_id)
                    <button
    type="button"
    @click.prevent="Alpine.store('favorites').toggle({{ $item->id }})"
    class="absolute top-2 right-2 z-50 flex items-center justify-center overflow-hidden"
    aria-label="Toggle favorite"
>
    <span
        x-show="Alpine.store('favorites').has({{ $item->id }})"
        class="text-red-500 leading-none"
    >
       ♥️
    </span>

    <span
        x-show="!Alpine.store('favorites').has({{ $item->id }})"
        class="text-gray-200 drop-shadow-lg leading-none"
    >
        🤍
    </span>
</button>

                        @endif
                    @endauth

                </div>

                <!-- CONTENT -->
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

                        <a
                            href="{{ route('listing.single', $item->id) }}"
                            class="text-blue-600 font-semibold"
                        >
                            More →
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <p class="text-gray-600 text-center col-span-full">
                No listings found
            </p>
        @endforelse

    </div>
</div>

</x-app-layout>
