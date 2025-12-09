<x-app-layout>
    <div
        x-data="{
            favorites: [],
            listings: [],

            async init() {
                const res = await fetch('/api/favorites/ids', {
                    credentials: 'same-origin'
                });

                this.favorites = await res.json();

                // sync with Alpine store (used by hearts everywhere)
                Alpine.store('favorites').list = this.favorites;

                this.loadFavorites();
            },

            async loadFavorites() {
                if (this.favorites.length === 0) {
                    this.listings = [];
                    return;
                }

                const response = await fetch(
                    '/api/listing?ids=' + this.favorites.join(','),
                    { credentials: 'same-origin' }
                );

                const json = await response.json();
                this.listings = json.data ?? json;
            }
        }"
        x-init="init()"
        class="container mx-auto px-4 mt-10"
    >

        <h1 class="text-3xl font-bold mb-6">My Favorites</h1>

        <!-- No favorites -->
        <template x-if="favorites.length === 0">
            <p class="text-gray-600">You have no favorite listings.</p>
        </template>

        <!-- Favorites grid -->
        <div
            x-show="listings.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
            <template x-for="item in listings" :key="item.id">
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <div class="relative">
                        <img
                            :src="item.photos?.length
                                ? `/storage/${item.photos[0].failo_url}`
                                : 'https://via.placeholder.com/300'"
                            class="w-full h-48 object-cover"
                        >

                        <!-- ❤️ HEART -->
                        <button
                            @click="
                                Alpine.store('favorites').toggle(item.id);
                                favorites = Alpine.store('favorites').list;
                                loadFavorites();
                            "
                            class="absolute top-2 right-2"
                        >
                            <span
                                x-show="favorites.includes(item.id)"
                                class="text-red-500 text-2xl"
                            >♥️</span>

                            <span
                                x-show="!favorites.includes(item.id)"
                                class="text-gray-300 text-2xl"
                            >🤍</span>
                        </button>
                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-semibold" x-text="item.pavadinimas"></h2>
                        <p class="text-gray-500 text-sm line-clamp-2" x-text="item.aprasymas"></p>

                        <div class="flex justify-between mt-3">
                            <span class="text-green-600 font-bold" x-text="item.kaina + ' €'"></span>
                            <a :href="'/listing/' + item.id" class="text-blue-600">More →</a>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>
</x-app-layout>
