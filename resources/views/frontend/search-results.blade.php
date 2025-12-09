<x-app-layout>

    <div class="container mx-auto px-4 mt-10">

        {{-- Applied Filters --}}
        @php 
            $filters = array_filter($filters); 
        @endphp

        @if (!empty($filters))
            <div class="flex flex-wrap gap-2 mb-6">
                
                @foreach ($filters as $key => $value)

                    @php
                        $newFilters = $filters;
                        unset($newFilters[$key]);
                        $query = http_build_query($newFilters);

                        // Convert filter key to readable name:
                        $labels = [
                            'category_id' => 'Category',
                            'tipas'       => 'Type',
                            'min_price'   => 'Min Price',
                            'max_price'   => 'Max Price',
                            'q'           => 'Search',
                            'sort'        => 'Sort',
                            'city_id'     => 'City',
                        ];

                        if ($key === 'sort') {
                            $value = match ($value) {
                                'newest'     => 'Newest first',
                                'oldest'     => 'Oldest first',
                                'price_asc'  => 'Price: Low to High',
                                'price_desc' => 'Price: High to Low',
                                default      => $value,
                            };
                        }

                        $label = $labels[$key] ?? ucfirst($key);

                        // Convert filter values to readable options
                        if ($key === 'category_id') {
                            $value = \App\Models\Category::find($value)?->pavadinimas ?? $value;
                        }

                        if ($key === 'tipas') {
                            $value = $value === 'preke' ? 'Product' : 'Service';
                        }

                        if ($key === 'city_id') {
                            $value = \App\Models\City::find($value)?->pavadinimas ?? $value;
                        }

                        if ($key === 'sort') {
                            $value = match ($value) {
                                'newest'     => 'Newest first',
                                'oldest'     => 'Oldest first',
                                'price_asc'  => 'Price: Low to High',
                                'price_desc' => 'Price: High to Low',
                                default      => $value,
                            };
                        }
                    @endphp

                    <a href="{{ route('search.listings') }}?{{ $query }}"
                       class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center gap-2">
                        <span>{{ $label }}: {{ $value }}</span>
                        <span class="font-bold">✕</span>
                    </a>

                @endforeach

                {{-- Clear all --}}
                <a href="{{ route('search.listings') }}"
                   class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold">
                    Clear all
                </a>

            </div>
        @endif

        <!-- Listings Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            @forelse ($listings as $item)
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <div class="relative">

                       @if($item->photos->isNotEmpty())
    <img
        src="{{ asset('storage/' . $item->photos->first()->failo_url) }}"
        class="w-full h-48 object-cover"
    >
@else
    <img
        src="https://via.placeholder.com/300x200?text=No+Image"
        class="w-full h-48 object-cover"
    >
@endif
                        <!-- Favorite Button -->
                        <button
                            @click="Alpine.store('favorites').toggle({{ $item->id }})"
                            class="absolute top-2 right-2">

                            <span x-show="Alpine.store('favorites').list.includes({{ $item->id }})" class="text-red-500 text-2xl">
                                ♥️
                            </span>

                            <span x-show="!Alpine.store('favorites').list.includes({{ $item->id }})" class="text-gray-200 drop-shadow-lg text-2xl leading-none">
                                🤍
                            </span>

                        </button>

                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-1">{{ $item['pavadinimas'] }}</h2>

                        <p class="text-gray-500 text-sm line-clamp-2">
                            {{ $item['aprasymas'] }}
                        </p>

                        <div class="flex justify-between items-center mt-3">
                            <span class="text-green-600 font-bold text-lg">
                                {{ $item['kaina'] }} €
                            </span>

                            <a href="/listing/{{ $item['id'] }}"
                               class="text-blue-600 font-semibold">
                                More →
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <p class="text-gray-600 text-center w-full">No results found.</p>
            @endforelse

        </div>

    </div>

</x-app-layout>
