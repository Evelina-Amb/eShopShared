<x-app-layout>
    <div class="container mx-auto px-4 mt-10">

        <h1 class="text-3xl font-bold mb-6">My Listings</h1>

        @if($listings->isEmpty())
            <p class="text-gray-600">You haven't posted any listings yet.</p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            x-data="{
                listings: {{ $listings->toJson() }},

                deleteListing(id) {
                    if (!confirm('Are you sure you want to delete this listing?')) return;

                    fetch('/api/listing/' + id, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(() => {
                        this.listings = this.listings.filter(l => l.id !== id);
                    });
                }
            }">

            <template x-for="item in listings" :key="item.id">
                <div class="bg-white shadow rounded overflow-hidden">

                    <!-- IMAGE -->
                    <img
    :src="item.photos?.[0]
        ? `/storage/${item.photos[0].failo_url}`
        : 'https://via.placeholder.com/300'"
    class="w-full h-48 object-cover"
/>


                    <div class="p-4">

                        <!-- TITLE -->
                        <h2 class="text-lg font-semibold mb-2" x-text="item.pavadinimas"></h2>

                        <!-- DESCRIPTION -->
                        <p class="text-gray-500 text-sm line-clamp-2" x-text="item.aprasymas"></p>

                        <!-- PRICE -->
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-green-600 font-bold text-lg" x-text="item.kaina + ' €'"></span>
                        </div>

                        <!-- QUANTITY + RENEWABLE STATUS -->
                        <div class="mt-2 text-sm">
                            <template x-if="item.tipas === 'preke'">
                                <div>
                                    <strong>Stock:</strong>
                                    <span
                                        :class="item.kiekis == 0 ? 'text-red-600 font-bold' : ''"
                                        x-text="item.kiekis"></span>

                                    <template x-if="item.is_renewable == 1">
                                        <span class="text-green-600 ml-1">(renewable)</span>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex justify-between items-center mt-4">
                            <a :href="'/listing/' + item.id + '/edit'" class="text-blue-600 font-semibold hover:underline">
                                Edit
                            </a>

                            <button @click="deleteListing(item.id)" class="text-red-600 font-semibold hover:underline">
                                Delete
                            </button>
                        </div>

                    </div>

                </div>
            </template>

        </div>
    </div>
</x-app-layout>
