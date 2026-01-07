<x-app-layout>
    <div
        x-data="{
            listings: [],
            loading: true,

            async load() {
                try {
                    const res = await fetch('/api/favorites/my', {
                        credentials: 'include',
                        headers: { Accept: 'application/json' },
                    });

                    this.listings = res.ok ? await res.json() : [];

                } catch (e) {
                    console.error('Failed loading favorites', e);
                    this.listings = [];
                } finally {
                    this.loading = false;
                }
            }
        }"
        x-init="load()"
        class="container mx-auto px-4 mt-10"
    >

        <h1 class="text-3xl font-bold mb-6">Išsaugoti</h1>

        <template x-if="loading">
            <p class="text-gray-500">Kraunami mėgstamiausi…</p>
        </template>

        <template x-if="!loading && listings.length === 0">
            <p class="text-gray-600">Neturite mėgstamų skelbimų.</p>
        </template>

        <div
            x-show="!loading && listings.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
            <template x-for="item in listings" :key="item.id">
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <!-- IMAGE + HEART -->
                    <div class="relative">
                        <img
                            :src="item.photos?.length
                                ? `/storage/${item.photos[0].failo_url}`
                                : 'https://via.placeholder.com/300'"
                            class="w-full h-48 object-cover"
                        >

                        <button
                            @click="
                                Alpine.store('favorites').toggle(item.id);
                                load();
                            "
                            class="absolute top-2 right-2 text-red-500 text-2xl"
                            title="Pašalinti iš mėgstamiausių"
                        >
                            ♥️
                        </button>
                    </div>

                    <!-- CONTENT -->
                    <div class="p-4">
                        <h2 class="font-semibold" x-text="item.pavadinimas"></h2>

                        <p
                            class="text-sm text-gray-500 line-clamp-2"
                            x-text="item.aprasymas"
                        ></p>

                        <div class="flex justify-between mt-3">
                            <span
                                class="text-green-600 font-bold"
                                x-text="item.kaina + ' €'"
                            ></span>

                            <a
                                :href="'/listing/' + item.id"
                                class="text-blue-600"
                            >
                                Plačiau →
                            </a>
                        </div>
                    </div>

                </div>
            </template>
        </div>

    </div>
</x-app-layout>
