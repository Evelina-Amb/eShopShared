<x-app-layout>

<div
    x-data
    x-init="Alpine.store('favorites').load()"
    class="container mx-auto px-3 sm:px-4 mt-6 sm:mt-8"
>
    <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,320px))] gap-4 sm:gap-6 justify-center">

        @forelse ($listings as $item)
            <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition flex flex-col">

                <!-- IMAGE + HEART -->
                <div class="relative">

                    @if($item->photos->isNotEmpty())
                        <img
                            src="{{ asset('storage/' . $item->photos->first()->failo_url) }}"
                            class="w-full h-44 sm:h-48 object-cover"
                        >
                    @else
                        <img
                            src="https://via.placeholder.com/300"
                            class="w-full h-44 sm:h-48 object-cover"
                        >
                    @endif

                    @auth
                        @if(auth()->id() !== $item->user_id)
<button
    type="button"
    @click.prevent="Alpine.store('favorites').toggle({{ $item->id }})"
    class="absolute top-2 right-2 z-30 w-10 h-10 sm:w-9 sm:h-9 flex items-center justify-center overflow-hidden"
    aria-label="Pažymėti kaip mėgstamą"
>
    <span
        x-show="Alpine.store('favorites').has({{ $item->id }})"
        class="text-red-500 text-2xl leading-none"
    >
        ♥️
    </span>

    <span
        x-show="!Alpine.store('favorites').has({{ $item->id }})"
        class="text-gray-200 text-2xl leading-none"
    >
        🤍
    </span>
</button>
                        @endif
                    @endauth

                </div>

                <!-- CONTENT -->
                <div class="p-3 sm:p-4 flex flex-col flex-1">
                    <h2 class="text-base sm:text-lg font-semibold mb-1 leading-snug">
                        {{ $item->pavadinimas }}
                    </h2>

                    <p class="text-gray-500 text-sm line-clamp-2 flex-1">
                        {{ $item->aprasymas }}
                    </p>

                    <div class="flex justify-between items-center mt-3">
                        <span class="text-green-600 font-bold text-base sm:text-lg">
                            {{ $item->kaina }} €
                        </span>

                        <a
                            href="{{ route('listing.single', $item->id) }}"
                            class="text-blue-600 font-semibold text-sm sm:text-base"
                        >
                           Plačiau →
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <p class="text-gray-600 text-center col-span-full">
                Skelbimų nerasta
            </p>
        @endforelse

    </div>
</div>

</x-app-layout>
