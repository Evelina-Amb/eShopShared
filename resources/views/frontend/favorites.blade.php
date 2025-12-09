<x-app-layout>
    <div
        x-data="{
            favorites: Alpine.store('favorites').list,
            listings: [],
            loading: true,

            async loadFavorites() {
                if (this.favorites.length === 0) {
                    this.loading = false;
                    return;
                }

                try {
                    const response = await fetch('/api/listing?ids=' + this.favorites.join(','));
                    const data = await response.json();
                    this.listings = data.data ?? [];
                } catch (e) {
                    console.error('Failed to load favorites', e);
                } finally {
                    this.loading = false;
                }
            }
        }"
        x-init="loadFavorites()"
        class="container mx-auto px-4 mt-10"
    >

        <h1 class="text-3xl font-bold mb-6">My Favorites</h1>

        <!-- Loading state -->
        <template x-if="loading">
            <p class="text-gray-500">Loading favorites…</p>
        </template>

        <!-- No favorites -->
        <template x-if="!loading && favorites.length === 0">
            <p class="text-gray-600">You have no favorite listings.</p>
        </template>

        <!-- Favorites grid -->
        <div
            x-show="!loading && listings.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
            <template x-for="item in listings" :key="item.id">
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <!-- Image -->
                    <div class="relative">
                        <img
                            :src="item.photos?.length
                                ? `/storage/${item.photos[0].failo_url}`
                                : 'https://via.placeholder.com/300x200?text=No+Image'"
                            class="w-full h-48 object-cover"
                        >

                        <!-- Favorite toggle -->
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
                            >
                                ♥️
                            </span>

                            <span
                                x-show="!favorites.includes(item.id)"
                                class="text-gray-300 text-2xl"
                            >
                                🤍
                            </span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h2
                            class="text-lg font-semibold mb-1"
                            x-text="item.pavadinimas"
                        ></h2>

                        <p
                            class="text-gray-500 text-sm line-clamp-2"
                            x-text="item.aprasymas"
                        ></p>

                        <div class="flex justify-between items-center mt-3">
                            <span
                                class="text-green-600 font-bold text-lg"
                                x-text="item.kaina + ' €'"
                            ></span>

                            <a
                                :href="'/listing/' + item.id"
                                class="text-blue-600 font-semibold"
                            >
                                More →
                            </a>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>
</x-app-layout>
