<x-app-layout>
    <div
        x-data="{
            listings: [],
            loading: true,

            async loadFavorites() {
                try {
                    const res = await fetch('/api/favorites');
                    const json = await res.json();
                    this.listings = json.data ?? [];
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            async removeFavorite(favoriteId) {
                try {
                    await fetch(`/api/favorites/${favoriteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name=csrf-token]')
                                .content
                        }
                    });

                    this.listings = this.listings.filter(f => f.id !== favoriteId);
                } catch (e) {
                    console.error('Failed to remove favorite', e);
                }
            }
        }"
        x-init="loadFavorites()"
        class="container mx-auto px-4 mt-10"
    >

        <h1 class="text-3xl font-bold mb-6">My Favorites</h1>

        <!-- Loading -->
        <template x-if="loading">
            <p class="text-gray-500">Loading favorites…</p>
        </template>

        <!-- Empty -->
        <template x-if="!loading && listings.length === 0">
            <p class="text-gray-600">You have no favorite listings.</p>
        </template>

        <!-- Grid -->
        <div
            x-show="!loading && listings.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
            <template x-for="fav in listings" :key="fav.id">
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <!-- Image + Heart -->
                    <div class="relative">
                        <img
                            :src="fav.skelbimas.photos?.length
                                ? `/storage/${fav.skelbimas.photos[0].failo_url}`
                                : 'https://via.placeholder.com/300x200?text=No+Image'"
                            class="w-full h-48 object-cover"
                        >

                        <!-- HEART ❤️ -->
                        <button
                            @click="removeFavorite(fav.id)"
                            class="absolute top-2 right-2 text-red-500 text-2xl"
                            title="Remove from favorites"
                        >
                            ♥️
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h2
                            class="text-lg font-semibold mb-1"
                            x-text="fav.skelbimas.pavadinimas"
                        ></h2>

                        <p
                            class="text-gray-500 text-sm line-clamp-2"
                            x-text="fav.skelbimas.aprasymas"
                        ></p>

                        <div class="flex justify-between items-center mt-3">
                            <span
                                class="text-green-600 font-bold text-lg"
                                x-text="fav.skelbimas.kaina + ' €'"
                            ></span>

                            <a
                                :href="'/listing/' + fav.skelbimas.id"
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
