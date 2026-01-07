<x-app-layout>

    <div class="container mx-auto px-3 sm:px-4 mt-6 sm:mt-10">

        {{-- Applied Filters --}}
        @php 
            $filters = array_filter($filters); 
        @endphp

        @if (!empty($filters))
            <div class="flex flex-wrap gap-2 mb-4 sm:mb-6">

                @foreach ($filters as $key => $value)

                    @php
                        $newFilters = $filters;
                        unset($newFilters[$key]);
                        $query = http_build_query($newFilters);

                        // Convert filter key to readable name:
                        $labels = [
                            'category_id' => 'Kategorija',
                            'tipas'       => 'Tipas',
                            'min_price'   => 'Min. kaina',
                            'max_price'   => 'Maks. kaina',
                            'q'           => 'Paieška',
                            'sort'        => 'Rūšiavimas',
                            'city_id'     => 'Miestas',
                        ];

                        $label = $labels[$key] ?? ucfirst($key);

                        // Convert filter values to readable options
                        if ($key === 'category_id') {
                            $value = \App\Models\Category::find($value)?->pavadinimas ?? $value;
                        }

                        if ($key === 'tipas') {
                            $value = $value === 'preke' ? 'Prekė' : 'Paslauga';
                        }

                        if ($key === 'city_id') {
                            $value = \App\Models\City::find($value)?->pavadinimas ?? $value;
                        }

                        if ($key === 'sort') {
                            $value = match ($value) {
                                'newest'     => 'Naujausi pirmiausia',
                                'oldest'     => 'Seniausi pirmiausia',
                                'price_asc'  => 'Kaina: nuo mažiausios',
                                'price_desc' => 'Kaina: nuo didžiausios',
                                default      => $value,
                            };
                        }
                    @endphp

                    <a
                        href="{{ route('search.listings') }}?{{ $query }}"
                        class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center gap-2 text-sm"
                    >
                        <span>{{ $label }}: {{ $value }}</span>
                        <span class="font-bold">✕</span>
                    </a>

                @endforeach

                {{-- Clear all --}}
                <a
                    href="{{ route('search.listings') }}"
                    class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-sm"
                >
                   Išvalyti viską
                </a>

            </div>
        @endif

        <!-- Listings Grid -->
       <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,320px))] gap-4 sm:gap-6 justify-center">

            @forelse ($listings as $item)
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition flex flex-col">

                    <div class="relative">

                        @if($item->photos->isNotEmpty())
                            <img
                                src="{{ asset('storage/' . $item->photos->first()->failo_url) }}"
                                class="w-full h-44 sm:h-48 object-cover"
                            >
                        @else
                            <img
                                src="https://via.placeholder.com/300x200?text=No+Image"
                                class="w-full h-44 sm:h-48 object-cover"
                            >
                        @endif

                        {{-- FAVORITE BUTTON --}}
                        @auth
                            @if(auth()->id() !== $item->user_id)
                                <button
                                    type="button"
                                    @click.prevent="Alpine.store('favorites').toggle({{ $item->id }})"
                                    class="absolute top-2 right-2 z-20 w-9 h-9 flex items-center justify-center text-2xl"
                                    aria-label="Pažymėti kaip mėgstamą"
                                >
                                    <span
                                        x-show="Alpine.store('favorites').has({{ $item->id }})"
                                        class="text-red-500"
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

                    <div class="p-3 sm:p-4 flex flex-col flex-1">
                        <h2 class="text-base sm:text-lg font-semibold mb-1 leading-snug">
                            {{ $item['pavadinimas'] }}
                        </h2>

                        <p class="text-gray-500 text-sm line-clamp-2 flex-1">
                            {{ $item['aprasymas'] }}
                        </p>

                        <div class="flex justify-between items-center mt-3">
                            <span class="text-green-600 font-bold text-base sm:text-lg">
                                {{ $item['kaina'] }} €
                            </span>

                            <a
                                href="/listing/{{ $item['id'] }}"
                                class="text-blue-600 font-semibold text-sm sm:text-base"
                            >
                                Plačiau →
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <p class="text-gray-600 text-center w-full">
                    Rezultatų nerasta.
                </p>
            @endforelse

        </div>

    </div>

</x-app-layout>
