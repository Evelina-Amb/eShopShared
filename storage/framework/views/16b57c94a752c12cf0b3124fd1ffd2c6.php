<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <div 
        x-data="{
            favorites: Alpine.store('favorites').list,
            listings: [],

            async loadFavorites() {
                if (this.favorites.length === 0) return;

                const response = await fetch('/api/listing?ids=' + this.favorites.join(','));
                const data = await response.json();
                this.listings = data.data;
            }
        }"
        x-init="loadFavorites()"
        class="container mx-auto px-4 mt-10"
    >

        <h1 class="text-3xl font-bold mb-6">My Favorites</h1>

        <!-- No favorites -->
        <template x-if="favorites.length === 0">
            <p class="text-gray-600">You have no favorite listings.</p>
        </template>

        <!-- Favorites Grid -->
        <div 
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            x-show="listings.length > 0"
        >

            <template x-for="item in listings" :key="item.id">
                <div class="bg-white shadow rounded overflow-hidden hover:shadow-lg transition">

                    <div class="relative">

                        <img 
                            :src="item.listing_photo?.[0]?.failo_url || 'https://via.placeholder.com/300'"
                            class="w-full h-48 object-cover"
                        />

                        <!-- Favorite Button -->
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
    class="text-gray-300 text-2xl leading-none"
>♡</span>

                        </button>

                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-semibold mb-1" x-text="item.pavadinimas"></h2>

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

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\eShop\resources\views/frontend/favorites.blade.php ENDPATH**/ ?>